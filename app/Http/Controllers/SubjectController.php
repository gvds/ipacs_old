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
        $subject->delete();
        return redirect('/subjects');
    }

    public function enrol(Request $request, Subject $subject)
    {
        $monthPrior = Carbon::today()->subMonth()->toDateString();
        $validatedData = $request->validate([
            'enrolDate' => "required|date|before_or_equal:today|after:$monthPrior"
        ]);
        try {
            DB::beginTransaction();

            // Update subject record
            $response = $subject->enrol($validatedData['enrolDate']);
            if ($response !== true) {
                throw new \ErrorException("Subject $subject->subjectID failed to enrol : $response");
            }

            // Add event entries for the subject's arm to the event_subject table
            $arm = $subject->arm()->with('events')->first();
            $response = $subject->createArmEvents($arm);
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
            $response = $subject->switchArm($validatedData['switchArm']);
            if ($response !== true) {
                throw new \ErrorException("Subject $subject->subjectID failed to switch : $response");
            }

            // Add event entries for the subject's arm to the event_subject table
            $arm = \App\arm::with('events')->where('id',$validatedData['switchArm'])->first();
            $response = $subject->createArmEvents($arm);
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
