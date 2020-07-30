<?php

namespace App\Http\Controllers;

use App\sample;
use Illuminate\Http\Request;

class SampleController extends Controller
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
        $samples = $this->currentProject->samples;
        return view('samples.index', compact('samples'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tubeLabelTypes = ['1'=>'Adhesive','2'=>'FluidX 1ml','3'=>'FluidX 300ul'];
        $sampleTypes = $this->currentProject->samples->pluck('name','id')->prepend('','');
        return view('samples.create', compact('tubeLabelTypes','sampleTypes'));
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
            'name' => 'required|max:50',
            'primary' => 'required|boolean',
            'aliquots' => 'required|integer|min:1|max:20',
            'pooled' => 'required|boolean',
            'defaultVolume' => 'nullable|integer',
            'volumeUnit' => 'nullable',
            'transferDestination' => 'nullable|max:25',
            'transferSource' => 'nullable|max:25',
            'sampleGroup' => 'nullable|max:25',
            'tubeLabelType' => 'nullable|max:25',
            'storageSampleType' => 'nullable|max:25',
            'parentSampleType_id' => 'nullable|integer'
        ]);
        $validatedData['project_id'] = $this->currentProject->id;
        sample::create($validatedData);
        return redirect('samples');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function show(sample $sample)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function edit(sample $sample)
    {
        $tubeLabelTypes = ['1'=>'Adhesive','2'=>'FluidX 1ml','3'=>'FluidX 300ul'];
        $sampleTypes = $this->currentProject->samples->pluck('name','id')->prepend('','');
        return view('samples.edit', compact('sample','tubeLabelTypes','sampleTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, sample $sample)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:50',
            'primary' => 'required|boolean',
            'aliquots' => 'required|integer|min:1|max:20',
            'pooled' => 'required|boolean',
            'defaultVolume' => 'nullable|integer',
            'volumeUnit' => 'nullable',
            'transferDestination' => 'nullable|max:25',
            'transferSource' => 'nullable|max:25',
            'sampleGroup' => 'nullable|max:25',
            'tubeLabelType' => 'nullable|max:25',
            'storageSampleType' => 'nullable|max:25',
            'parentSampleType_id' => 'nullable|integer',
            'active' => 'required|boolean'
        ]);
        $sample->update($validatedData);
        return redirect('samples');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function destroy(sample $sample)
    {
        $sample->delete();
        return redirect('/samples');
    }
}
