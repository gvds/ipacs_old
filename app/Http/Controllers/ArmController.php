<?php

namespace App\Http\Controllers;

use App\arm;
use Illuminate\Http\Request;

class ArmController extends Controller
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
        $arms = arm::where('project_id', $this->currentProject->id)->orderBy('arm_num')->get();
        foreach ($arms as $armkey => $arm) {
            $switcharms = json_decode($arm->switcharms);
            if (isset($switcharms)) {
                foreach ($switcharms as $switchkey => $switcharm) {
                    $switcharms[$switchkey] = arm::find($switcharm)->name;
                }
                $arms[$armkey]->switcharms = implode(' || ',($switcharms)) ;
            }
        }
        return view('arms.index', compact('arms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $arms = $this->currentProject->arms;
        return view('/arms.create',compact('arms'));
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
            'name' => 'required|min:3|max:50',
            'redcap_arm_id' => 'nullable|integer',
            'arm_num' => 'required|integer',
            'manual_enrole' => 'required|in:0,1',
            'switcharms' => 'nullable'
        ]);
        $validatedData['project_id'] = $this->currentProject->id;
        $validatedData['switcharms'] = isset($validatedData['switcharms']) ? json_encode($validatedData['switcharms']) : null;
        arm::create($validatedData);
        return redirect('/arms');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\arm  $arm
     * @return \Illuminate\Http\Response
     */
    public function show(arm $arm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\arm  $arm
     * @return \Illuminate\Http\Response
     */
    public function edit(arm $arm)
    {
        $arms = $this->currentProject->arms->where('id','!=',$arm->id);
        $arm->switcharms = json_decode($arm->switcharms);
        return view('arms.edit', compact('arm','arms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\arm  $arm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, arm $arm)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:3|max:50',
            'redcap_arm_id' => 'nullable|integer',
            'arm_num' => 'required|integer',
            'manual_enrole' => 'required|in:0,1',
            'switcharms' => 'nullable'
        ]);
        $validatedData['switcharms'] = isset($validatedData['switcharms']) ? json_encode($validatedData['switcharms']) : null;
        $arm->update($validatedData);
        return redirect('/arms');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\arm  $arm
     * @return \Illuminate\Http\Response
     */
    public function destroy(arm $arm)
    {
        $arm->delete();
        return redirect('/arms');
    }
}
