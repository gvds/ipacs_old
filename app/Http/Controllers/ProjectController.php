<?php

namespace App\Http\Controllers;

use App\project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Integer;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = project::orderBy('project')->get();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $users = \App\User::orderBy('name')->get()->pluck('full_name', 'id')->prepend('', '');
        return view('projects.create', compact('users'));
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
            'project' => 'required|max:50|unique:projects,project',
            'redcapProject_id' => 'numeric|nullable|unique:projects,redcapProject_id',
            'owner' => 'required|numeric',
            'subject_id_prefix' => 'max:6|nullable',
            'subject_id_digits' => 'numeric|min:2|max:6',
            'storageProjectName' => 'max:15|nullable',
            'label_id' => 'max:40|required'
        ]);
        project::create($validatedData);

        // Create corrosponding Team entry

        return redirect('/project');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(project $project)
    {
        $users = \App\User::orderBy('name')->get()->pluck('full_name', 'id')->prepend('', '');
        return view('projects.edit', compact('project', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, project $project)
    {
        $validatedData = $request->validate([
            'project' => 'required|max:50|unique:projects,project,' . $project->id . ',id',
            'redcapProject_id' => 'numeric|nullable|unique:projects,redcapProject_id,' . $project->id . ',id',
            'owner' => 'required|numeric',
            'subject_id_prefix' => 'max:6|nullable',
            'subject_id_digits' => 'numeric|min:2|max:6',
            'storageProjectName' => 'max:15|nullable',
            'label_id' => 'max:40|required'
        ]);
        $project->update($validatedData);

        // Update corrosponding Team entry if necessary

        return redirect('/project');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(project $project)
    {
        $project->delete();

        // Delete corrosponding Team entry

        return redirect('/project');
    }

    // public function list(Request $request)
    // {
    //     // Need to restrict this to projects with Team to which this user belongs
    //     $projectlist = project::where('project','like','%' . $request->searchString . '%')->pluck('project','id');
    //     return $projectlist;
    // }

    public function selectList()
    {
        $projects = project::where('owner', Auth::user()->id)
        ->where('active', true)
        ->orderBy('project')
        ->get();
        return view('projectSelector', compact('projects'));
    }

    public function select(project $project)
    {
        if ($project->owner === auth()->user()->id) {
            session(['currentProject' => $project->id]);
        }
        
        return redirect('/');
    }
}
