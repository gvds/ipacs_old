<?php

namespace App\Http\Controllers;

use App\subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
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
        $arms = $currentProject->arms->where('manual_enrole')->pluck('name', 'id');
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
                    $arm = \App\arm::find($value)->first();
                    if ($arm->manual_enrole === 0) {
                        $fail('Subjects cannot be enroled into this ' . $attribute);
                    }
                },
                function ($attribute, $value, $fail) {  // Does this arm belong to the current project?
                    $arm = \App\arm::find($value)->first();
                    if ($arm->project_id !== request('currentProject')->id) {
                        $fail('This ' . $attribute . ' does not belong to the current project');
                    }
                },
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
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
        if ($subject->user_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to access this subject\'s record');
        }
        $switcharms = is_null($subject->arm->switcharms) ? [] : json_decode($subject->arm->switcharms);
        $switcharms = \App\arm::whereIn('id', $switcharms)->pluck('name', 'id');
        $eventstatus = \App\eventStatus::all();
        $events = $subject
            ->events()
            ->with('arm')
            ->orderBy('event_order')
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
        if ($subject->user_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to access this subject\'s record');
        }
        $subject->delete();
        return redirect('/subjects');
    }

    public function enrol(Request $request, Subject $subject)
    {
        if ($subject->user_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to access this subject\'s record');
        }
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

            // Update subject record
            $response = $subject->enrol($validatedData);
            if ($response !== true) {
                throw new \ErrorException("Subject $subject->subjectID failed to enrol : $response");
            }

            // Add event entries for the subject's arm to the event_subject table
            $arm = $subject->arm()->with('events')->first();
            $response = $subject->createArmEvents($arm,$validatedData['enrolDate']);
            if ($response !== true) {
                throw new \ErrorException("Events for $subject->subjectID could not be created : $response");
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
        return redirect()->back()->with('message', "Subject enrolled");
    }

    public function switch(Request $request, Subject $subject)
    {
        if ($subject->user_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to access this subject\'s record');
        }
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

            // Update subject record
            $response = $subject->switchArm($validatedData['switchArm'],$validatedData['switchDate']);
            if ($response !== true) {
                throw new \ErrorException("Subject $subject->subjectID failed to switch : $response");
            }

            // Add event entries for the subject's arm to the event_subject table
            $arm = \App\arm::with('events')->where('id',$validatedData['switchArm'])->first();
            $response = $subject->createArmEvents($arm,$validatedData['switchDate']);
            if ($response !== true) {
                throw new \ErrorException("Events for $subject->subjectID could not be created : $response");
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
        return redirect()->back()->with('message', "Subject switched");
    }

    public function reverseSwitch(Request $request, Subject $subject)
    {
        if ($subject->user_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to access this subject\'s record');
        }
        try {
            DB::beginTransaction();

            $currentArm = $subject->arm_id;
            $previousArm = $subject->previous_arm_id;
            // Update subject record
            $response = $subject->reverseArmSwitch();
            if ($response !== true) {
                throw new \ErrorException("Subject $subject->subjectID failed to reverse switch : $response");
            }

            // Delete current event entries and restore previous arm event entries in the event_subject table
            $response = $subject->revertArmSwitchEvents($currentArm,$previousArm);
            if ($response !== true) {
                throw new \ErrorException("Events for $subject->subjectID could not be reverted : $response");
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
        return redirect()->back()->with('message', "Arm switch reversed");
    }

    public function drop(Request $request, Subject $subject)
    {
        if ($subject->user_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to access this subject\'s record');
        }
        if ($subject->subject_status !== 1) {
            return redirect()->back()->with('error', "This subject cannot be dropped");
        }
        $subject->subject_status = 2;
        $subject->save();
        return redirect()->back()->with('warning', "Subject dropped");
    }

    public function restore(Request $request, Subject $subject)
    {
        if ($subject->user_id !== auth()->user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to access this subject\'s record');
        }
        if ($subject->subject_status !== 2) {
            return redirect()->back()->with('error', "This subject cannot be restored");
        }
        $subject->subject_status = 1;
        $subject->save();
        return redirect()->back()->with('message', "Subject Restored");
    }

    /**
     * Generate a search-list of the current user's subjects for retrieval
     *
     * @return \Illuminate\Http\Response
     */
    public function search($searchterm)
    {
        return subject::where('subjectID', 'like', $searchterm . '%')
            ->where('user_id', auth()->user()->id)
            ->pluck('subjectID', 'id')
            ->take(8);
    }
}
