<?php

namespace App\Http\Controllers;

use App\User;
use App\physicalUnit;
use App\unitDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PhysicalUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $physicalUnits = physicalUnit::join('unitDefinitions','physicalUnits.unitDefinition_id','unitDefinitions.id')
        // ->orderBy('unitDefinitions.storageType')
        // ->orderBy('unitDefinition_id')
        // ->orderBy('unitID')
        $physicalUnits = physicalUnit::orderBy('unitID')
            ->get();
        return view('storage.physical.index', compact('physicalUnits'));
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
        $adminlist = User::whereRoleIs('freezer_admin')->selectRaw('concat_ws(" ",firstname,surname) AS name,id')->pluck('name','id');
        return view('/storage.physical.create', compact('unitDefinition','adminlist'));
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
            'unitID' => 'required|min:5',
            'serial' => 'nullable|min:5|max:40',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        try {
            $physicalUnit = physicalUnit::create($validatedData);
        } catch (\Throwable $th) {
            return back()->with('error', 'Creation of Physical Unit failed: ' . $th->getMessage());
        }
        return redirect("/unitDefinition/" . $validatedData['unitDefinition_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\section  $section
     * @return \Illuminate\Http\Response
     */
    public function show(physicalUnit $physicalUnit)
    {
        $rackCount = 0;
        foreach ($physicalUnit->unitType->sections as $key => $section) {
            $rackCount += $section->rows * $section->columns;
        }
        $racks = array_fill_keys(range(1,$rackCount,1),0);
        $virtualUnits = $physicalUnit->virtualUnits;
        $boxDesignation = $physicalUnit->unitType->boxDesignation;
        foreach ($virtualUnits as $key => $virtualUnit) {
            for ($i=$virtualUnit->startRack; $i <= $virtualUnit->endRack ; $i++) {
                $racks[$i] = is_null($virtualUnit->startBox) ? 1 : 2; // Full [1] or partial [2] rack
            }
        }
        $projects = \App\project::where('active',1)->orderBy('project')->pluck('project','id')->prepend('','');
        return view('storage.physical.show', compact('physicalUnit','virtualUnits','projects','racks'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\section  $section
     * @return \Illuminate\Http\Response
     */
    public function edit(physicalUnit $physicalUnit)
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
    public function update(Request $request, physicalUnit $physicalUnit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\section  $section
     * @return \Illuminate\Http\Response
     */
    public function destroy(physicalUnit $physicalUnit)
    {
        $unitDefinition_id = $physicalUnit->unitDefinition_id;
        $physicalUnit->delete();
        return redirect("/unitDefinition/$unitDefinition_id");
    }

    public function toggleActive(physicalUnit $physicalUnit)
    {
        $physicalUnit->update(['available' => !$physicalUnit->available]);
        return back();
    }

}
