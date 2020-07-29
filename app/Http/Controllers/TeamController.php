<?php

namespace App\Http\Controllers;

use App\Team;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class TeamController extends Controller
{
    public $currentProject;

    public function __construct()
    {
        // $this->middleware('auth'); //Already done in router
        $this->middleware(function ($request, $next) {
            $this->currentProject = \App\project::find(session('currentProject', null));
            if (is_null($this->currentProject)) {
                return redirect('/')->with('warning', 'There is currently no selected project');
            }
            $user = auth()->user();
            if (!$user->isAbleTo('manage-teams', $this->currentProject->team->name)) {
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
        $currentProject = $this->currentProject;
        $teammembers = Team::find($currentProject->id)->users()
            ->select('users.*', 'name')
            ->leftJoin('sites', 'site_id', 'sites.id')
            ->get();

        // dd($teammembers);

        // $teammembers = Team::find($currentProject->id)->users()->with(['sites'=>function ($query) use ($currentProject){
        //     $query->where('team_user.team_id',$currentProject->id);
        // }])->get();

        // $teammembers = DB::table('team_user')
        // ->join('sites','team_user.team_id','sites.team_id')
        // ->join('users','team_user.user_id','users.id')
        // ->where('team_user.team_id',$currentProject->id)
        // ->where('team_user.user_id','users.id')
        // // ->join('users')
        // // ->join('sites')
        // ->get();

        // $teammembers = \App\User::with(['team_member_permissions' => function ($query) use ($currentProject){
        //     $query->where('team_id',$currentProject->id);
        // }])->get();

        // $teammembers = \App\Team::find($currentProject->id)->permission_users->unique('id');
        // foreach ($teammembers as $teammember) {
        //     $team_permissions = DB::table('permission_user')
        //     ->join('permissions', 'permission_id', 'id')
        //     ->where('user_id', $teammember->id)
        //     ->where('team_id', $currentProject->id)
        //     ->get();
        //     $teammember->team_permissions = $team_permissions;
        // }

        return view('/projects.team.index', compact('currentProject', 'teammembers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addmember()
    {
        $team = Team::findOrFail(session('currentProject'));
        $sites = $team->sites()->pluck('name', 'id')->prepend('', '');
        $currentUsers = ($team->users()->pluck('id'));
        $users = User::select(DB::raw("CONCAT(firstname,' ',surname) AS name"), 'id')->whereNotIn('id', $currentUsers)->orderBy('name')->pluck('name', 'id')->prepend('', '');
        return view('/projects.team.create', compact('users', 'sites'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storemember(Request $request)
    {
        $request->validate([
            'user' => 'required|integer|exists:App\User,id',
            'site' => 'nullable'
        ]);
        $team = Team::findOrFail(session('currentProject'));
        $team->users()->sync([$request->user => ['site_id' => $request->site]], false);
        return redirect('/team');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function showmember(User $user)
    {
        $team = Team::findOrFail(session('currentProject'));
        return view('projects.team.show', compact('user', 'team'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function editmember(User $user)
    {
        $team = Team::findOrFail(session('currentProject'));
        $user = $team->users()->where('users.id', $user->id)->first();
        $sites = $team->sites()->pluck('name', 'id')->prepend('', '');
        $permissions = \App\Permission::where('scope', 'project')->orderBy('display_name')->get();
        return view('projects.team.edit', compact('user', 'sites', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updatemember(Request $request, User $user)
    {
        $request->validate([
            'site' => 'nullable'
        ]);
        $team = Team::findOrFail(session('currentProject'));
        $team->users()->sync([$user->id => ['site_id' => $request->site]], false);
        return redirect('/team');
    }

    /**
     * Show the form for editing member permissions.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function editpermissions(User $user)
    {
        $team = Team::findOrFail(session('currentProject'));
        $user = $team->users()->where('users.id', $user->id)->first();
        $userpermissions = $user->team_member_permissions->where('pivot.team_id', $team->id)->pluck('display_name', 'id')->toArray();
        $permissions = \App\Permission::where('scope', 'project')->orderBy('display_name')->pluck('display_name', 'id');
        return view('projects.team.permissions', compact('user', 'permissions', 'userpermissions'));
    }

    /**
     * Update the member permissions in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updatepermissions(Request $request, User $user)
    {
        $requestData = $request->all();
        unset($requestData['_method'], $requestData['_token']);
        $validpermissions = \App\Permission::where('scope', 'project')->pluck('id');
        $validatedData = Validator::make(array_keys($requestData), [
            '*' => [
                Rule::in($validpermissions),
            ],
        ])->validate();
        $team = Team::findOrFail(session('currentProject'));
        $validatedData = array_flip($validatedData);
        foreach ($validatedData as $key => $value) {
            $validatedData[$key] = ['user_type' => 'App\User', 'team_id' => $team->id];
        }
        $user->team_member_permissions()->sync($validatedData);
        $user->flushCache();
        return redirect("/team/$user->id");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroymember(User $user)
    {
        $team = Team::findOrFail(session('currentProject'));
        $user->team_member_permissions()->detach();
        $team->users()->detach($user->id);
        $user->flushCache();
        return redirect('/team');
    }
}
