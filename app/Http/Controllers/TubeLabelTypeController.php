<?php

namespace App\Http\Controllers;

use App\sampletype;
use App\tubeLabelType;
use Illuminate\Http\Request;

use function PHPSTORM_META\override;

class TubeLabelTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentProject = request('currentProject');

        $project_tubeLabelTypes = \App\tubeLabelType::where('project_id', $currentProject->id)
            ->orderBy('tubeLabelType')
            ->get();
        $project_tubeLabelType_names = $project_tubeLabelTypes->pluck('tubeLabelType', 'id')->toArray();
        $project_tubeLabelType_formats = $project_tubeLabelTypes->pluck('barcodeFormat', 'id')->toArray();

        $generic_tubeLabelTypes = \App\tubeLabelType::whereNull('project_id')
            // ->whereNotIn('tubeLabelType', $project_tubeLabelType_names)
            ->orderBy('tubeLabelType')
            ->get();
        $generic_tubeLabelType_names = $generic_tubeLabelTypes->pluck('tubeLabelType', 'id')->toArray();

        foreach ($generic_tubeLabelTypes as $key => $generic_tubeLabelType) {
            $format_key = array_search($generic_tubeLabelType->tubeLabelType, $project_tubeLabelType_names);
            if ($format_key) {
                $generic_tubeLabelTypes[$key]['override'] = [$format_key => $project_tubeLabelType_formats[$format_key]];
                // $generic_tubeLabelTypes[$key]['override_id'] = $format_key;
                // dd($generic_tubeLabelTypes);
            }
        }

        return view('tubelabeltypes.index', compact('project_tubeLabelTypes', 'generic_tubeLabelTypes', 'generic_tubeLabelType_names'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tubelabeltypes.create');
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
            'tubeLabelType' => 'required|min:3',
            'preregister' => 'required|boolean',
            'registration' => 'required|in:range,single',
            'barcodeFormat' => 'required|starts_with:^|ends_with:$|min:4'
        ]);
        $validatedData['project_id'] = session('currentProject');
        $generic_tubeLabelType = tubeLabelType::whereNull('project_id')
            ->where('tubeLabelType', $validatedData['tubeLabelType'])
            ->first();
        $overrideTubeLabelType = tubeLabelType::create($validatedData);
        if ($generic_tubeLabelType) {
            $sampletypes = sampletype::where('project_id', session('currentProject'))
                ->where('tubeLabelType_id', $generic_tubeLabelType->id)
                ->get();
            foreach ($sampletypes as $sampletype) {
                $sampletype->tubeLabelType_id = $overrideTubeLabelType->id;
                $sampletype->save();
            }
        }
        return redirect('/tubelabeltype');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\tubeLabelType  $tubelabeltype
     * @return \Illuminate\Http\Response
     */
    public function edit(tubeLabelType $tubelabeltype)
    {
        return view('tubelabeltypes.edit', compact('tubelabeltype'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\tubeLabelType  $tubelabeltype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, tubeLabelType $tubelabeltype)
    {
        $validatedData = $request->validate([
            'tubeLabelType' => 'required|min:3',
            'preregister' => 'required|boolean',
            'registration' => 'required|in:range,single',
            'barcodeFormat' => 'required|starts_with:^|ends_with:$|min:4'
        ]);
        $validatedData['project_id'] = session('currentProject');
        $tubelabeltype->update($validatedData);
        return redirect('/tubelabeltype');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\tubeLabelType  $tubelabeltype
     * @return \Illuminate\Http\Response
     */
    public function destroy(tubeLabelType $tubelabeltype)
    {
        if ($tubelabeltype->project_id) {
            $generic_tubeLabelType = tubeLabelType::whereNull('project_id')
                ->where('tubeLabelType', $tubelabeltype->tubeLabelType)
                ->first();
            $sampletypes = sampletype::where('project_id', session('currentProject'))
                ->where('tubeLabelType_id', $tubelabeltype->id)
                ->get();
            foreach ($sampletypes as $sampletype) {
                $sampletype->tubeLabelType_id = $generic_tubeLabelType->id;
                $sampletype->save();
            }
        }
        $tubelabeltype->delete();
        return redirect('/tubelabeltype');
    }
    public function override(tubeLabelType $tubelabeltype)
    {
        return view('tubelabeltypes.create', compact('tubelabeltype'));
    }
}
