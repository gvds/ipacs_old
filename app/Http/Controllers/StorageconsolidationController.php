<?php

namespace App\Http\Controllers;

use App\Storageconsolidation;
use App\virtualUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageconsolidationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate(['virtualunit' => 'required|exists:virtualUnits,id']);
        $virtualUnit = virtualUnit::with('physicalUnit')
            ->find($request->virtualunit);
        $storageconsolidations = Storageconsolidation::with('user')
            ->where('virtualUnit_id', $request->virtualunit)
            ->latest()
            ->get();
        return view('storage.consolidation.index', compact('virtualUnit', 'storageconsolidations'));
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
        $request->validate(['virtualunit' => 'required|exists:virtualUnits,id']);
        $virtualUnit = virtualUnit::find($request->virtualunit);
        try {
            DB::beginTransaction();
            $virtualUnit->consolidate();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors("Consolidation of virtual unit $virtualUnit->virtualUnit in project $virtualUnit->project failed :- " . $th->getMessage());
        }
        return back()->with('message', "Virtual unit $virtualUnit->virtualUnit has been consolidated");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Storageconsolidation  $storageconsolidation
     * @return \Illuminate\Http\Response
     */
    public function show(Storageconsolidation $storageconsolidation)
    {
        return $storageconsolidation->generateReport();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Storageconsolidation  $storageconsolidation
     * @return \Illuminate\Http\Response
     */
    public function edit(Storageconsolidation $storageconsolidation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Storageconsolidation  $storageconsolidation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Storageconsolidation $storageconsolidation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Storageconsolidation  $storageconsolidation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Storageconsolidation $storageconsolidation)
    {
        //
    }
}
