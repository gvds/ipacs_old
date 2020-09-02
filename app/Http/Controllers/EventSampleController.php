<?php

namespace App\Http\Controllers;

use App\event_sample;
use Illuminate\Http\Request;

class EventSampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'barcode' => 'nullable|regex:/^[A-Z]{0,6}\d{3,8}$/|exists:event_sample,barcode'
        ]);
        if (array_key_exists('barcode', $validatedData)) {
            $sample = event_sample::join('sampletypes', 'sampletype_id', '=', 'sampletypes.id')
                ->select('event_sample.*')
                ->where('barcode', $validatedData['barcode'])
                ->where('project_id', session('currentProject'))
                ->first();
            if (!is_null($sample)) {
                $subject = $sample->event_subject->subject;
                if ($subject->site_id !== auth()->user()->project_site) {
                    return back()->withErrors('This sample does not belong to your site');
                }
                return redirect("/samples/$sample->id");
            } else {
                return view('samples.index')->withErrors("Sample " . $validatedData['barcode'] . " was not found in this project");
            }
        }
        return view('samples.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function show(event_sample $event_sample)
    {
        if ($event_sample->samplestatus_id == 0) {
            return back()->withErrors("Sample barcode " . $event_sample->barcode . " is currently unassigned");
        }
        $subject = $event_sample->event_subject->subject;
        if ($subject->project_id !== session('currentProject')) {
            return back()->withErrors('This sample does not belong to the current project');
        }
        if ($subject->site_id !== auth()->user()->project_site) {
            return back()->withErrors('This sample does not belong to your site');
        }
        return view('samples.show', compact('event_sample'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function edit(event_sample $event_sample)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, event_sample $event_sample)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function destroy(event_sample $event_sample)
    {
        //
    }

    /**
     * Unlog the specified sample.
     *
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function unlog(event_sample $event_sample)
    {
        $event_sample->samplestatus_id = 0;
        $event_sample->save();
        return redirect('/samples')->with('message', "Sample $event_sample->barcode has been unlogged");
    }

    /**
     * Update the specified sample's volume.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function volumeUpdate(Request $request, event_sample $event_sample)
    {
        $validatedData = $request->validate([
            'volume' => 'required|numeric'
        ]);
        $event_sample->volume = $validatedData['volume'];
        $event_sample->save();
        return back()->with('message', "Volume has been updated");
    }
}
