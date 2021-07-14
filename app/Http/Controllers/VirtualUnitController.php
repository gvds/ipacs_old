<?php

namespace App\Http\Controllers;

use App\physicalUnit;
use App\virtualUnit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VirtualUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $physicalUnits = \App\physicalUnit::orderBy('unitType')
            ->orderBy('unitID')
            ->get('unitID', 'id');
        return view('storage.virtual.index', compact('physicalUnits'));
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
        $validatedData  = $request->validate([
            'physicalUnit_id' => 'required|exists:physicalUnits,id',
            'project_id' => 'required|exists:projects,id',
            'storageSampleType' => 'required|exists:sampletypes,storageSampleType',
            'virtualUnit' => [
                'required',
                Rule::unique('virtualUnits')->where(function ($query) use ($request) {
                    return $query->where('project_id', $request->project_id);
                })
            ],
            'section' => 'required|integer|min:1',
            'selection_type' => 'required|in:full,partial',
            'startRack' => 'required|integer|min:1',
            'endRack' => 'required|integer|gte:startRack',
            'startBox' => 'required_with:selection_type',
            'endBox' => 'required_with:selection_type|gte:startBox',
            'rackCapacity' => 'required|integer|min:1',
            'boxCapacity' => 'required|integer|min:1'
        ]);
        try {
            $project = \App\project::find($validatedData['project_id']);
            $validatedData['project'] = $project->storageProjectName;
            $unitDefinition = physicalUnit::find($validatedData['physicalUnit_id'])->unitType;
            if ($validatedData['selection_type'] == 'partial') {
                if (ord($validatedData['startBox']) > ord($validatedData['endBox'])) {
                    throw new Exception("End Box must be ≥ Start Box", 1);
                }
                $validatedData['endRack'] = $validatedData['startRack'];
                if ($unitDefinition->boxDesignation === 'Alpha') {
                    $validatedData['startBox'] = chr(65 + $validatedData['startBox']);
                    $validatedData['endBox'] = chr(65 + $validatedData['endBox']);
                }
            } else {
                if ($unitDefinition->boxDesignation === 'Alpha') {
                    $validatedData['startBox'] = 'A';
                    $validatedData['endBox'] = chr(64 + $validatedData['rackCapacity'] - 1);
                } else {
                    $validatedData['startBox'] = '1';
                    $validatedData['endBox'] = $validatedData['rackCapacity'] - 1;
                }
            }
            $boxes = range($validatedData['startBox'], $validatedData['endBox']);
            DB::beginTransaction();
            $virtualUnit = virtualUnit::create(Arr::except($validatedData, ['selection_type']));
            for ($rack = $validatedData['startRack']; $rack <= $validatedData['endRack']; $rack++) {
                foreach ($boxes as $box) {
                    for ($position = 1; $position <= $validatedData['boxCapacity']; $position++) {
                        $location = new \App\location;
                        $location->virtualUnit_id = $virtualUnit->id;
                        $location->storageProjectName = $project->storageProjectName;
                        $location->rack = $rack;
                        $location->box = $box;
                        $location->position = $position;
                        $location->save();
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('input')->with('error', 'Failed to create Virtual Unit: ' . $th->getMessage());
        }
        return back()->with('input')->with('message', 'Virtual Unit created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\virtualUnit  $virtualUnit
     * @return \Illuminate\Http\Response
     */
    public function show(physicalUnit $physicalUnit)
    {
        $virtualUnits = virtualUnit::all();
        return view('storage.virtual.index', compact('virtualUnits'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\virtualUnit  $virtualUnit
     * @return \Illuminate\Http\Response
     */
    public function edit(virtualUnit $virtualUnit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\virtualUnit  $virtualUnit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, virtualUnit $virtualUnit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\virtualUnit  $virtualUnit
     * @return \Illuminate\Http\Response
     */
    public function destroy(virtualUnit $virtualUnit)
    {
        $virtualUnit->delete();
        return back()->with('message', "Virtual Unit $virtualUnit->virtualUnit deleted");
    }

    public function toggleActive(virtualUnit $virtualUnit)
    {
        $virtualUnit->update(['active' => !$virtualUnit->active]);
        return back();
    }
}
