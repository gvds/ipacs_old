<?php

namespace App\Http\Controllers;

use App\section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'unitDefinition_id' => 'required|exists:unitDefinitions,id'
        ]);
        $unitDefinition = \App\unitDefinition::find($request->unitDefinition_id);
        $section = count($unitDefinition->sections) + 1;
        return view('/storage.section.create', compact('unitDefinition','section'));
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
            'unitDefinition_id' => 'required|exists:unitDefinitions,id',
            'section' => 'required|integer|min:1',
            'rows' => 'required|integer|min:1',
            'columns' => 'required|integer|min:1',
            'boxes' => 'required|integer|min:1',
            'positions' => 'required|integer|min:1',
        ]);
        try {
            $unitDefinition = section::create($validatedData);
        } catch (\Throwable $th) {
            return back()->with('error', 'Creation of Section failed: ' . $th->getMessage());
        }
        return redirect("/unitDefinitions/" . $validatedData['unitDefinition_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\section  $section
     * @return \Illuminate\Http\Response
     */
    public function show(section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\section  $section
     * @return \Illuminate\Http\Response
     */
    public function edit(section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\section  $section
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, section $section)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\section  $section
     * @return \Illuminate\Http\Response
     */
    public function destroy(section $section)
    {
        $unitDefinition_id = $section->unitDefinition_id;
        $section->delete();
        return redirect("/unitDefinitions/$unitDefinition_id");
    }

}
