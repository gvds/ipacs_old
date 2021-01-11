<?php

namespace App\Http\Controllers;

use App\datafile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatafileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentProject = \App\project::find(session('currentProject'));
        $datafiles = datafile::where('project_id',session('currentProject'))
        ->orderBy('fileset')
        ->orderBy('created_at')
        ->get();
        return view('datafiles.index', compact('datafiles', 'currentProject'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'fileset' => 'nullable|integer'
        ]);
        if (is_null($request->fileset)) {
            $fileset = datafile::where('project_id', session('currentProject'))->max('fileset') + 1;
        } else {
            $fileset = $request->fileset;
        }
        $files = datafile::where('project_id', session('currentProject'))
            ->where('fileset', $fileset)
            ->orderBy('id')
            ->get();
        return view('datafiles.create', compact('fileset', 'files'));
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
            'file' => 'required|file',
            'generationDate' => 'required|date',
            'lab' => 'required|max:100',
            'platform' => 'required|max:100',
            'opperator' => 'required|max:100',
            'fileset' => 'required|integer',
            'description' => 'nullable|max:500',
            'filetype' => 'required|max:40',
            'software' => 'required|max:40',
            'owner' => 'required|max:60'
        ]);
        $filename = $validatedData['file']->getClientOriginalName();
        if (datafile::where('filename',$filename)->where('project_id',session('currentProject'))->exists()) {
            return back()->withInput()->withErrors('Duplicate filename');
        }
        $filesize = filesize($validatedData['file']);
        $hash = hash_file('sha256',$validatedData['file']);
        $path = $validatedData['file']->storeAs("/" . session('currentProject'),$filename,'local');
        $file = new datafile;
        $file->filename = $filename;
        $file->resource = $path;
        $file->project_id = session('currentProject');
        $file->user_id = auth()->user()->id;
        $file->site_id = auth()->user()->project_site;
        $file->generationDate = $validatedData['generationDate'];
        $file->lab = $validatedData['lab'];
        $file->platform = $validatedData['platform'];
        $file->opperator = $validatedData['opperator'];
        $file->description = $validatedData['description'];
        $file->fileset = $validatedData['fileset'];
        $file->filetype = $validatedData['filetype'];
        $file->software = $validatedData['software'];
        $file->owner = $validatedData['owner'];
        $file->hash = $hash;
        $file->filesize = $filesize;
        $file->save();
        return redirect('/datafiles')->with('message',"$filename uploaded (" . (round($filesize/1024**2,2)) . " MB)");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\datafile  $datafile
     * @return \Illuminate\Http\Response
     */
    public function show(datafile $datafile)
    {
        $currentProject = \App\project::find(session('currentProject'));
        return view('datafiles.show', compact('datafile', 'currentProject'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\datafile  $datafile
     * @return \Illuminate\Http\Response
     */
    public function edit(datafile $datafile)
    {
        if (!$datafile->exists() || $datafile->project_id !== session('currentProject')) {
            return back()->withErrors('This file does not exist in the current project');
        }
        return view('/datafiles.edit', compact('datafile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\datafile  $datafile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, datafile $datafile)
    {
        $validatedData = $request->validate([
            'generationDate' => 'required|date',
            'lab' => 'required|max:100',
            'platform' => 'required|max:100',
            'opperator' => 'required|max:100',
            'description' => 'nullable|max:500',
            'filetype' => 'required|max:40',
            'software' => 'required|max:40',
            'owner' => 'required|max:60'
        ]);
        $datafile->update($validatedData);
        return redirect('/datafiles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\datafile  $datafile
     * @return \Illuminate\Http\Response
     */
    public function destroy(datafile $datafile)
    {
        Storage::disk('local')->delete($datafile->resource);
        $datafile->delete();
        return redirect('/datafiles');
    }

    /**
     * Retrieve the file from the repository
     * 
     * @param  \App\datafile  $datafile
     * @return  \Illuminate\Http\Response
     */
    public function download(datafile $datafile)
    {
        return Storage::download($datafile->resource, $datafile->filename);
    }
}
