<?php

namespace App\Http\Controllers;

use App\unitDefinition;
use Illuminate\Http\Request;

class UnitDefinitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unitDefinitions = unitDefinition::with('sections')->get();
        return view('storage.unitdef.index', compact('unitDefinitions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('storage.unitdef.create');
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
            'unitType' => 'required|Min:5|Max:100',
            'sectionLayout' => 'required|in:vertical,horizontal',
            'boxDesignation' => 'required|in:alpha,numeric',
            'storageType' => 'required|in:minus80,ln,minus20,bios',
            'rackOrder' => 'required|in:columnwise,rowwise',
            'orientation' => 'required|in:vertical,horizontal'
        ]);
        try {
            $unitDefinition = unitDefinition::create($validatedData);
        } catch (\Throwable $th) {
            return back()->with('error','Creation of Unit Definition failed: ' . $th->getMessage());
        }
        return redirect("/unitDefinitions/$unitDefinition->id");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\unitDefinition  $unitDefinition
     * @return \Illuminate\Http\Response
     */
    public function show(unitDefinition $unitDefinition)
    {
        $sections = $unitDefinition->sections;
        $physicalUnits = $unitDefinition->physicalUnits;
        return view('storage.unitdef.show', compact('unitDefinition','sections', 'physicalUnits'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\unitDefinition  $unitDefinition
     * @return \Illuminate\Http\Response
     */
    public function edit(unitDefinition $unitDefinition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\unitDefinition  $unitDefinition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, unitDefinition $unitDefinition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\unitDefinition  $unitDefinition
     * @return \Illuminate\Http\Response
     */
    public function destroy(unitDefinition $unitDefinition)
    {
        $unitDefinition->delete();
        return redirect('/unitDefinitions');
    }
}
