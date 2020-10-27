<?php

namespace App\Http\Controllers;

use App\event_subject;
use App\subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('subjects.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentProject = request('currentProject');
        $arms = $currentProject->arms->where('manual_enrol')->pluck('name', 'id');
        return view('subjects.generate', compact('arms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'records' => 'required|integer|min:1|max:20',
            'arm' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {  // Does this arm accept new IDs?
                    $arm = \App\arm::find($value);
                    if ($arm->manual_enrol === 0) {
                        $fail('Subjects cannot be enroled into this ' . $attribute);
                    }
                },
                function ($attribute, $value, $fail) {  // Does this arm belong to the current project?
                    $arm = \App\arm::find($value);
                    if ($arm->project_id !== request('currentProject')->id) {
                        $fail('This ' . $attribute . ' does not belong to the current project');
                    }
                },
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        if (empty(auth()->user()->projectSite)) {
            return redirect()->back()->with('error', 'Subject IDs cannot be created as your site has not been set')->withInput();
        }
        $response = subject::createSubjects($validator->valid());
        if ($response !== 0) {
            return redirect()->back()->with('error', $response . ' - No new IDs created')->withInput();
        }

        return redirect('/')->with('message', $validator->valid()['records'] . ' new subject IDs created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(subject $subject)
    {
        $subject->checkAccessPermission();

        $switcharms = is_null($subject->arm->switcharms) ? [] : json_decode($subject->arm->switcharms);
        $switcharms = \App\arm::whereIn('id', $switcharms)->pluck('name', 'id');
        $eventstatus = \App\eventStatus::all();
        $events = $subject
            ->events()
            ->with('arm')
            ->orderBy('offset')
            ->orderBy('eventDate')
            ->orderBy('iteration')
            ->get()
            ->sortBy(function ($event) {
                return $event->arm->arm_num;
            });

        return view('subjects.show', compact('subject', 'events', 'eventstatus', 'switcharms'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit(subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, subject $subject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(subject $subject)
    {
        $subject->checkAccessPermission();

        $subject->delete();
        return redirect('/subjects');
    }

    public function enrol(Request $request, Subject $subject)
    {
        $subject->checkAccessPermission();

        $monthPrior = Carbon::today()->subMonth()->toDateString();
        $validatedData = $request->validate([
            'enrolDate' => "required|date|before_or_equal:today|after:$monthPrior",
            'firstname' => "present|max:30",
            'surname' => "present|max:30",
            'address1' => "present|max:50",
            'address2' => "present|max:50",
            'address3' => "present|max:50",
        ]);
        try {
            DB::beginTransaction();

            $subject->enrol($validatedData);

            // Schedule event dates
            $events = $subject->arm()->first()->events()->get();
            foreach ($events as $event) {
                $response = $subject->setEventDates($event, $validatedData['enrolDate']);
                if ($response !== true) {
                    throw new \ErrorException("Event dates for $subject->subjectID could not be set : $response");
                }
            }

            if (isset($request->currentProject->redcapProject_id)) {
                $subject->createREDCapRecord();
            }

            DB::commit();

            \App\Label::addEventsToLabelQueue();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
        return redirect()->back()->with('message', "Subject enrolled");
    }

    public function switch(Request $request, Subject $subject)
    {
        $subject->checkAccessPermission();

        $monthPrior = Carbon::today()->subMonth()->toDateString();
        $switcharms = json_decode($subject->arm->switcharms);
        $validatedData = $request->validate([
            'switchDate' => "required|date|before_or_equal:today|after:$monthPrior",
            'switchArm' => [
                'required',
                Rule::in($switcharms),
            ],
        ]);
        try {
            DB::beginTransaction();

            $subject->switchArm($validatedData['switchArm'], $validatedData['switchDate']);

            $subject->cancelOutstandingEvents();

            // Add event entries for the subject's arm to the event_subject table
            $arm = $subject->arm()->with('events')->first();
            $response = $subject->createArmEvents($arm);
            if ($response !== true) {
                throw new \ErrorException("Events for $subject->subjectID could not be created : $response");
            }
            // Schedule event dates
            $events = $arm->events()->get();
            foreach ($events as $event) {
                $response = $subject->setEventDates($event, $validatedData['switchDate']);
            }

            if (isset($request->currentProject->redcapProject_id)) {
                $subject->createREDCapRecord();
            }

            DB::commit();

            \App\Label::addEventsToLabelQueue();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
        return redirect()->back()->with('message', "Subject switched");
    }

    public function reverseSwitch(Request $request, Subject $subject)
    {
        $subject->checkAccessPermission();

        try {
            DB::beginTransaction();

            $currentArm = $subject->arm_id;
            $previousArm = $subject->previous_arm_id;

            $subject->reverseArmSwitch();

            $subject->revertArmSwitchEvents($currentArm, $previousArm);

            DB::commit();

            \App\Label::addEventsToLabelQueue();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
        return redirect()->back()->with('message', "Arm switch reversed");
    }

    public function drop(Request $request, Subject $subject)
    {
        $subject->checkAccessPermission();

        if ($subject->subject_status !== 1) {
            return redirect()->back()->with('error', "This subject cannot be dropped");
        }
        $subject->subject_status = 2;
        $subject->save();
        return redirect()->back()->with('warning', "Subject dropped");
    }

    public function restore(Request $request, Subject $subject)
    {
        $subject->checkAccessPermission();

        if ($subject->subject_status !== 2) {
            return redirect()->back()->with('error', "This subject cannot be restored");
        }
        $subject->subject_status = 1;
        $subject->save();
        return redirect()->back()->with('message', "Subject Restored");

        \App\Label::addEventsToLabelQueue();
    }

    /**
     * Generate a search-list of the current user's subjects for retrieval
     *
     * @return \Illuminate\Http\Response
     */
    public function search($searchterm)
    {
        return subject::where('subjectID', 'like', "%{$searchterm}%")
            ->where('project_id', session('currentProject'))
            ->where('user_id', auth()->user()->id)
            ->pluck('subjectID', 'id')
            ->take(8);
    }

    /**
     * Add an instance of a repeatable event
     *
     * @return \Illuminate\Http\Response
     */
    public function addEvent(Request $request, Subject $subject)
    {
        $subject->checkAccessPermission();
        $validatedData = $request->validate([
            'event_subject_id' => "required|integer|exists:event_subject,id",
        ]);
        try {
            DB::beginTransaction();
            $event_subject = event_subject::find($validatedData['event_subject_id']);
            $lastInstance = event_subject::where('subject_id', $subject->id)
                ->where('event_id', $event_subject->event_id)
                ->orderBy('iteration', 'desc')
                ->first();
            if (is_null($lastInstance)) {
                throw new \ErrorException("No previous iterations of this event could be found");
            }
            $event = \App\event::where('id', $event_subject->event_id)->with('arm')->first();
            $subject->events()->attach($event->id, [
                'labelStatus' => 0,
                'iteration' => $lastInstance->iteration + 1,
                'eventDate' => today(),
                'minDate' => $lastInstance->minDate,
                'maxDate' => $lastInstance->maxDate,
                'eventstatus_id' => 2
            ]);

            DB::commit();

            \App\Label::addEventsToLabelQueue();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('message', "New event iteration created");
    }
}
