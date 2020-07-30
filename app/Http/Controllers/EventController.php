<?php

namespace App\Http\Controllers;

use App\event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public $currentProject;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->currentProject = \App\project::find(session('currentProject', null));
            if (is_null($this->currentProject)) {
                return redirect('/')->with('warning', 'There is currently no selected project');
            }
            $user = auth()->user();
            if (!$user->isAbleTo('administer-projects', $this->currentProject->team->name)) {
                return redirect('/')->with('error', 'You do not have the necessary access rights');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = $this->currentProject->events;
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $arms = $this->currentProject->arms->pluck('name','id');
        return view('events.create',compact('arms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:3|max:50',
            'redcap_event_id' => 'nullable|integer',
            'arm_id' => 'required|exists:arms,id',
            'redcap_event_id' => 'nullable|integer',
            'autolog' => 'required|boolean',
            'offset' => 'required|integer|min:0',
            'offset_min' => 'required|integer|min:0',
            'offset_max' => 'required|integer|min:0',
            'name_labels' => 'required|integer|min:0',
            'subject_event_labels' => 'required|integer|min:0',
            'study_id_labels' => 'required|integer|min:0',
            'event_order' => 'required|integer|min:0',
            'active' => 'required|boolean'
        ]);
        event::create($validatedData);
        return redirect('/events');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(event $event)
    {
        $arms = $this->currentProject->arms->pluck('name','id');
        return view('events.edit', compact('event','arms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, event $event)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:3|max:50',
            'redcap_event_id' => 'nullable|integer',
            'arm_id' => 'required|exists:arms,id',
            'redcap_event_id' => 'nullable|integer',
            'autolog' => 'required|boolean',
            'offset' => 'required|integer|min:0',
            'offset_min' => 'required|integer|min:0',
            'offset_max' => 'required|integer|min:0',
            'name_labels' => 'required|integer|min:0',
            'subject_event_labels' => 'required|integer|min:0',
            'study_id_labels' => 'required|integer|min:0',
            'event_order' => 'required|integer|min:0',
            'active' => 'required|boolean'
        ]);
        $event->update($validatedData);
        return redirect('/events');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(event $event)
    {
        $event->delete();
        return redirect('/events');
    }
}
