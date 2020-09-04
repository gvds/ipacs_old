<?php

namespace App\Http\Controllers;

use App\event;
use Illuminate\Http\Request;

class EventController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentProject = request('currentProject');
        $events = $currentProject->events;
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentProject = request('currentProject');
        $arms = $currentProject->arms->pluck('name','id');
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
            // 'redcap_event_id' => 'nullable|integer',
            'arm_id' => 'required|exists:arms,id',
            'autolog' => 'required|boolean',
            'offset' => 'required|integer|min:0',
            'offset_ante_window' => 'required|integer|min:0',
            'offset_post_window' => 'required|integer|min:0',
            'name_labels' => 'required|integer|min:0',
            'subject_event_labels' => 'required|integer|min:0',
            'study_id_labels' => 'required|integer|min:0',
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
        $currentProject = request('currentProject');
        $arms = $currentProject->arms->pluck('name','id');
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
            // 'redcap_event_id' => 'nullable|integer',
            'arm_id' => 'required|exists:arms,id',
            'autolog' => 'required|boolean',
            'offset' => 'required|integer|min:0',
            'offset_ante_window' => 'required|integer|min:0',
            'offset_post_window' => 'required|integer|min:0',
            'name_labels' => 'required|integer|min:0',
            'subject_event_labels' => 'required|integer|min:0',
            'study_id_labels' => 'required|integer|min:0',
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
