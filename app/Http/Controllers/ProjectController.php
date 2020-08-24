<?php

namespace App\Http\Controllers;

use App\project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $query = "select app_title, project_id from redcap_projects";
        $linked_redcap_projects = project::where('redcapProject_id','<>','null')->pluck('redcapProject_id')->toArray();
        if (count($linked_redcap_projects) > 0) {
            $query .= " where project_id not in (" . implode(",",$linked_redcap_projects) . ")";
        }
        $redcap_projects = DB::connection('redcap')
        ->select($query);
        $redcap_projects = collect($redcap_projects)->pluck('app_title','project_id')->prepend('', '');
        $users = \App\User::orderBy('firstname')->get()->pluck('full_name', 'id')->prepend('', '');
        return view('projects.create', compact('users','redcap_projects'));
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

        $project = project::create($validatedData);

        // Create corrosponding Team entry
        Team::create([
            'id' => $project->id,
            'name' => Str::snake($validatedData['project']),
            'display_name' => $validatedData['project'],
        ]);

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
        $current_project_id = $project->redcapProject_id;
        $query = "select app_title, project_id from redcap_projects";
        $linked_redcap_projects = project::where('redcapProject_id','<>','null')
        ->pluck('redcapProject_id')
        ->toArray();
        if (count($linked_redcap_projects) > 0) {
            $query .= " where project_id = $current_project_id or project_id not in (" . implode(",",$linked_redcap_projects) . ")";
        }
        $redcap_projects = DB::connection('redcap')
        ->select($query);
        $redcap_projects = collect($redcap_projects)->pluck('app_title','project_id')->prepend('', '');
        $users = \App\User::orderBy('firstname')->get()->pluck('full_name', 'id')->prepend('', '');
        return view('projects.edit', compact('project', 'users','redcap_projects'));
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

        // Update corrosponding Team entry if necessary
        if ($validatedData['project'] != $project->project) {
            Team::find($project->id)->update([
                'name' => Str::snake($validatedData['project']),
                'display_name' => $validatedData['project']
            ]);
        }

        $project->update($validatedData);
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
        Team::find($project->id)->delete();

        session()->forget('currentProject');

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
        $teams = \App\User::find(Auth::user()->id)->teams()->pluck('id');
        $projects = project::where('active', true)
            ->where(function ($query) use ($teams) {
                $query->whereIn('id', $teams)
                    ->orWhere('owner', Auth::user()->id);
            })
            ->orderBy('project')
            ->get();
        return view('projectSelector', compact('projects'));
    }

    public function select(project $project)
    {
        if (in_array($project->id, \App\User::find(Auth::user()->id)->teams()->pluck('id')->toArray()) or $project->owner === Auth::user()->id) {
            session(['currentProject' => $project->id]);
        } else {
            return redirect('/')->with('error', 'You do not have access to that project');
        }
        return redirect('/');
    }
}
