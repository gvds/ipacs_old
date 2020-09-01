<?php

namespace App\Http\Controllers;

use App\project;
use App\site;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RedcapController extends Controller
{

    private function curl(array $params)
    {
        $user = auth()->user()->teams()->where('teams.id', session('currentProject'))->first();
        $redcap_api_token = $user->pivot->redcap_api_token;

        $fields = array(
            'token'   => $redcap_api_token,
            'format'  => 'json'
        );

        $fields = array_merge($fields, $params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, config('services.redcap.url'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // Set to TRUE for production use
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

        return curl_exec($ch);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $query = "select app_title, project_id from redcap_projects";
        $linked_redcap_projects = project::where('redcapProject_id', '<>', 'null')->pluck('redcapProject_id')->toArray();
        if (count($linked_redcap_projects) > 0) {
            $query .= " where project_id not in (" . implode(",", $linked_redcap_projects) . ")";
        }
        $query .= " order by app_title";
        $redcap_projects = DB::connection('redcap')
            ->select($query);
        $redcap_projects = collect($redcap_projects)->pluck('app_title', 'project_id')->prepend('', '');
        $users = \App\User::orderBy('firstname')->get()->pluck('full_name', 'id')->prepend('', '');
        return view('projects.redcap.create', compact('redcap_projects', 'users'));
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
            'redcapProject_id' => 'numeric|required|unique:projects,redcapProject_id',
            'owner' => 'required|numeric',
            'subject_id_prefix' => 'max:6|nullable',
            'subject_id_digits' => 'numeric|min:2|max:6',
            'storageProjectName' => 'max:15|nullable',
            'label_id' => 'max:40|required'
        ]);

        try {
            DB::beginTransaction();

            $redcap_project = DB::connection('redcap')->select("select * from redcap_projects where project_id = " . $validatedData['redcapProject_id']);
            $validatedData['project'] = $redcap_project[0]->app_title;

            $user = User::find($validatedData['owner']);

            $redcap_user = $this->getREDCapUser($validatedData['redcapProject_id'], $user->username);

            if (empty($redcap_user)) {
                throw new Exception('This user was not found in the REDCap project');
            }


            // Create the project entry
            $project = project::create($validatedData);

            $token = $this->getOrGenerateToken($redcap_user, $project, $user);

            // Create sites entries from REDCap DAGS
            $redcap_dags = DB::connection('redcap')->select("select * from redcap_data_access_groups where project_id = " . $validatedData['redcapProject_id']);

            foreach ($redcap_dags as $dag) {
                $site = site::create([
                    'project_id' => $project->id,
                    'name' => $dag->group_name,
                ]);
                if ($dag->group_id == $redcap_user[0]->group_id) {
                    $user_site = $site->id;
                }
            }
            // Create corrosponding Team entry
            $team = \App\Team::create([
                'id' => $project->id,
                'name' => Str::snake($validatedData['project']),
                'display_name' => $validatedData['project'],
            ]);

            // Create linking table entry for user in team
            $team->users()->sync([$request->owner => ['site_id' => $user_site ?? null, 'redcap_api_token' => $token]], false);

            // Create arms
            $redcap_arms = $this->arms();
            foreach ($redcap_arms as $redcap_arm) {
                $arm = \App\arm::create([
                    'project_id' => $project->id,
                    'name' => $redcap_arm->name,
                    'arm_num' => $redcap_arm->arm_num
                ]);

                // Create events
                $redcap_events = $this->events([$arm->arm_num]);
                foreach ($redcap_events as $redcap_event) {
                    $event = \App\event::create([
                        'arm_id' => $arm->id,
                        'name' => $redcap_event->event_name,
                        'offset' => $redcap_event->day_offset,
                        'offset_ante_window' => $redcap_event->offset_min,
                        'offset_post_window' => $redcap_event->offset_max,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->withInput()->withErrors($th->getMessage());
        }

        return redirect('/project');
    }

    public function edit(project $project)
    {
        $current_project_id = $project->redcapProject_id;
        $query = "select app_title, project_id from redcap_projects";
        $linked_redcap_projects = project::where('redcapProject_id', '<>', 'null')->pluck('redcapProject_id')->toArray();
        if (count($linked_redcap_projects) > 0) {
            $query .= " where project_id not in (" . implode(",", $linked_redcap_projects) . ")";
        }
        $redcap_projects = DB::connection('redcap')
            ->select($query);
        $redcap_projects = collect($redcap_projects)->pluck('app_title', 'project_id')->prepend('', '');
        $users = \App\Team::find($project->id)->users()->orderBy('firstname')->get()->pluck('full_name', 'id')->prepend('', '');
        return view('projects.redcap.edit', compact('project', 'redcap_projects', 'users'));
    }

    public function update(Request $request, project $project)
    {
        $validatedData = $request->validate([
            'owner' => 'required|numeric',
            'subject_id_prefix' => 'max:6|nullable',
            'subject_id_digits' => 'numeric|min:2|max:6',
            'storageProjectName' => 'max:15|nullable',
            'label_id' => 'max:40|required'
        ]);

        try {
            DB::beginTransaction();

            $user = User::find($validatedData['owner']);

            $redcap_user = $this->getREDCapUser($project->redcapProject_id, $user->username);

            if (empty($redcap_user)) {
                throw new Exception('This user was not found in the REDCap project');
            }

            $token = $this->getOrGenerateToken($redcap_user, $project, $user);

            $project->update($validatedData);

            // Retrieve corrosponding Team entry
            $team = \App\Team::find($project->id);

            // Update linking table entry for user in team - add API token
            $team->users()->updateExistingPivot($user->id, ['redcap_api_token' => $token], false);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->withInput()->withErrors($th->getMessage());
        }
        return redirect('/project');
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

    private function getREDCapUser($redcapProject_id, $username)
    {
        return DB::connection('redcap')->select(
            "SELECT * from redcap_user_rights left join redcap_data_access_groups on 
                    redcap_user_rights.group_id = redcap_data_access_groups.group_id and 
                    redcap_user_rights.project_id = redcap_data_access_groups.project_id 
                    where redcap_user_rights.project_id = $redcapProject_id and 
                    redcap_user_rights.username = '$username'"
        );
    }

    public function arms()
    {
        $params = [
            'content' => 'arm'
        ];
        $arms = $this->curl($params);
        return collect(json_decode($arms));
    }

    public function events($arms = [])
    {
        $params = [
            'content' => 'event',
            'arms' => $arms
        ];
        $events = $this->curl($params);
        return collect(json_decode($events));
    }

    public function users()
    {
        $params = [
            'content' => 'user'
        ];
        $users = $this->curl($params);
        return collect(json_decode($users));
    }

    public function project()
    {
        $params = [
            'content' => 'project'
        ];
        $project = $this->curl($params);
        return collect(json_decode($project));
    }

    public function usersdirect(Request $request)
    {
        $redcapProject_id = $request->currentProject->redcapProject_id;
        $users = DB::connection('redcap')->select("select * from redcap_user_rights where project_id = $redcapProject_id");
        dd($users);
    }

    public function projectlist(Request $request)
    {
        $redcap_projects = DB::connection('redcap')->select("select * from redcap_projects");
        dd($redcap_projects);
    }
}
