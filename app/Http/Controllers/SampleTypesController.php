<?php

namespace App\Http\Controllers;

use App\sampletype;
use App\site;
use Illuminate\Http\Request;

class SampleTypesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentProject = request('currentProject');
        $sampletypes = $currentProject->sampletypes;
        return view('sampletypes.index', compact('sampletypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentProject = request('currentProject');

        $project_tubeLabelTypes = \App\tubeLabelType::where('project_id', $currentProject->id)
            ->orderBy('tubeLabelType')
            ->pluck('tubeLabelType', 'id');

        $generic_tubeLabelTypes = \App\tubeLabelType::whereNull('project_id')
            ->whereNotIn('tubeLabelType', $project_tubeLabelTypes)
            ->orderBy('tubeLabelType')
            ->pluck('tubeLabelType', 'id')
            ->prepend('', '');

        $tubeLabelTypes = $generic_tubeLabelTypes->union($project_tubeLabelTypes);
        $destinations = site::where('project_id', session('currentProject'))
            ->pluck('name', 'name')
            ->prepend('', '');

        $sampleTypes = $currentProject->sampletypes->pluck('name', 'id')->prepend('', '');

        return view('sampletypes.create', compact('tubeLabelTypes', 'sampleTypes', 'destinations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentProject = request('currentProject');
        $validatedData = $request->validate([
            'name' => 'required|max:50',
            'primary' => 'required|boolean',
            'aliquots' => 'required|integer|min:1|max:20',
            'pooled' => 'required|boolean',
            'defaultVolume' => 'nullable|numeric',
            'volumeUnit' => 'nullable',
            'transferDestination' => 'nullable|max:25',
            'sampleGroup' => 'nullable|max:25',
            'tubeLabelType_id' => 'nullable|integer',
            'storageType' => 'nullable|required_with:storageSampleType|in:Internal,BiOS,Nexus',
            'storageSampleType' => 'nullable|required_unless:storageType,,BiOS|min:3|max:25',
            'parentSampleType_id' => 'nullable|integer'
        ]);
        $validatedData['project_id'] = $currentProject->id;
        sampletype::create($validatedData);
        return redirect('sampletypes');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\sample  $sampletype
     * @return \Illuminate\Http\Response
     */
    public function show(sampletype $sampletype)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\sample  $sampletype
     * @return \Illuminate\Http\Response
     */
    public function edit(sampletype $sampletype)
    {
        $currentProject = request('currentProject');

        $project_tubeLabelTypes = \App\tubeLabelType::where('project_id', $currentProject->id)
            ->orderBy('tubeLabelType')
            ->pluck('tubeLabelType', 'id');

        $generic_tubeLabelTypes = \App\tubeLabelType::whereNull('project_id')
            ->whereNotIn('tubeLabelType', $project_tubeLabelTypes)
            ->orderBy('tubeLabelType')
            ->pluck('tubeLabelType', 'id')
            ->prepend('', '');

        $tubeLabelTypes = $generic_tubeLabelTypes->union($project_tubeLabelTypes);

        $sampleTypes = $currentProject->sampletypes->pluck('name', 'id')->prepend('', '');

        $destinations = site::where('project_id', session('currentProject'))
            ->pluck('name', 'name')
            ->prepend('', '');

        return view('sampletypes.edit', compact('sampletype', 'tubeLabelTypes', 'sampleTypes', 'destinations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\sample  $sampletype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, sampletype $sampletype)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:50',
            'primary' => 'required|boolean',
            'aliquots' => 'required|integer|min:1|max:20',
            'pooled' => 'required|boolean',
            'defaultVolume' => 'nullable|numeric',
            'volumeUnit' => 'nullable',
            'transferDestination' => 'nullable|max:25',
            'sampleGroup' => 'nullable|max:25',
            'tubeLabelType_id' => 'nullable|integer',
            'storageType' => 'nullable|required_with:storageSampleType|in:Internal,BiOS,Nexus',
            'storageSampleType' => 'nullable|required_unless:storageType,,BiOS|min:3|max:25',
            'parentSampleType_id' => 'nullable|integer',
            'active' => 'required|boolean'
        ]);
        $sampletype->update($validatedData);
        return redirect('sampletypes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\sample  $sampletype
     * @return \Illuminate\Http\Response
     */
    public function destroy(sampletype $sampletype)
    {
        $sampletype->delete();
        return redirect('/sampletypes');
    }
}
