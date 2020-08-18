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
    public function index()
    {
        return view('sample')
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
        if()
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
}
