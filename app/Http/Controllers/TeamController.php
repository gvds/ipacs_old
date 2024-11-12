<?php

namespace App\Http\Controllers;

use App\subject;
use App\Team;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class TeamController extends Controller
{
    // public $currentProject;

    // public function __construct()
    // {
    //     // $this->middleware('auth'); //Already done in router
    //     $this->middleware(function ($request, $next) {
    //         $this->currentProject = \App\project::find(session('currentProject', null));
    //         if (is_null($this->currentProject)) {
    //             return redirect('/')->with('warning', 'There is currently no selected project');
    //         }
    //         $user = auth()->user();
    //         if (!$user->isAbleTo('manage-teams', $this->currentProject->team->name)) {
    //             return redirect('/')->with('error', 'You do not have the necessary access rights');
    //         }
    //         return $next($request);
    //     });
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $currentProject = $request->currentProject;
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
    public function addmember(Request $request)
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
        try {
            DB::beginTransaction();
            $project = $request->currentProject;
            $user = User::findOrFail($request->user);
            if (!empty($project->redcapProject_id)) {
                $redcap_user = $this->getREDCapUser($project->redcapProject_id, $user->username);
                if (empty($redcap_user)) {
                    throw new Exception('This user was not found in the REDCap project');
                }
                $token = $this->getOrGenerateToken($redcap_user, $project, $user);
            }
            $team = Team::findOrFail($project->id);
            $team->users()->sync([$user->id => ['site_id' => $request->site]], false);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors($th->getMessage());
        }
        return redirect('/team');
    }

    private function getREDCapUser($redcapProject_id, $username)
    {
        return DB::connection('redcap')->select(
            "SELECT * from
                    redcap_user_rights
                left join
                    redcap_data_access_groups
                on
                    redcap_user_rights.group_id = redcap_data_access_groups.group_id
                and
                    redcap_user_rights.project_id = redcap_data_access_groups.project_id
                where
                    redcap_user_rights.project_id = $redcapProject_id
                and
                    redcap_user_rights.username = '$username'"
        );
    }

    private function getOrGenerateToken($redcap_user, $project, $user)
    {
        if (is_null($redcap_user[0]->api_token)) {
            for ($i = 0; $i < 5; $i++) {
                $token = strtoupper(bin2hex(random_bytes(16)));
                $duplicate = DB::connection('redcap')->select(
                    "SELECT count(api_token) as found FROM redcap_user_rights WHERE api_token = '$token'"
                );
                if (!$duplicate[0]->found) {
                    break;
                }
                throw new Exception('Could not create unique API token for this user in the REDCap database');
            }
            DB::connection('redcap')->update(
                "UPDATE redcap_user_rights SET api_token = '$token' WHERE
                redcap_user_rights.project_id = $project->redcapProject_id and
                redcap_user_rights.username = '$user->username'"
            );
            return $token;
        } else {
            return $redcap_user[0]->api_token;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function showmember(Request $request, User $user)
    {
        $currentProject = $request->currentProject;
        $team = Team::findOrFail(session('currentProject'));
        $users = $team->subject_managers
            ->where('id', '!=', $user->id)
            ->where('pivot.site_id', $user->currentSite[0]->id)
            ->pluck('fullname', 'id')
            ->prepend('', '');
        return view('projects.team.show', compact('user', 'team', 'users'));
    }

    public function transfersubjects(Request $request, User $user)
    {
        $validated = $request->validate([
            'transferee' => 'required|exists:users,id'
        ]);


        $transferee = User::find($validated['transferee']);
        if ($transferee->currentSite->first()->id !== $user->currentSite->first()->id) {
            return back()->withErrors('The transferee is not in the same site as this user');
        }
        // if (!$transferee->isAbleTo('manage-subjects', $request->currentProject->project)) {
        //     return back()->withErrors('This transferee cannot manage subjects');
        // }
        $subjects = subject::where('site_id', $user->currentSite->first()->id)
            ->where('user_id', $user->id)
            ->get();
        try {
            DB::beginTransaction();
            foreach ($subjects as $subject) {
                $subject->transferTo($transferee);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors('Subject transfer failed: ' . $th->getMessage());
        }

        return redirect("/team/$user->id")->with('message', "All subjects transfered to $transferee->fullname");
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
            'site' => 'nullable',
            'redcap_api_token' => 'nullable|regex:/^[0-9A-F]{32}$/'
        ]);
        $team = Team::findOrFail(session('currentProject'));
        $team->users()->sync([$user->id => [
            'site_id' => $request->site,
            'redcap_api_token' => $request->redcap_api_token
        ]], false);
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
        unset($requestData['_method'], $requestData['_token'], $requestData['currentProject']);
        $validpermissions = \App\Permission::where('scope', 'project')->pluck('id');
        $validatedData = Validator::make(array_keys($requestData), [
            '*' => [
                Rule::in($validpermissions),
            ],
        ])->validate();
        $team = Team::findOrFail(session('currentProject'));
        $user->syncPermissions($validatedData, $team->id);
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
