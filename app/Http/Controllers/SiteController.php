<?php

namespace App\Http\Controllers;

use App\site;
use Illuminate\Http\Request;

class SiteController extends Controller
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
        $sites = site::where('project_id',$this->currentProject->id)->orderBy('name')->get();
        return view('sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('/sites.create');
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
            'name' => 'required|min:3|max:20',
        ]);
        $validatedData['team_id'] = $this->currentProject->id;
        site::create($validatedData);
        return redirect('/sites');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\site  $site
     * @return \Illuminate\Http\Response
     */
    public function show(site $site)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(site $site)
    {
        return view('sites.edit', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, site $site)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:3|max:20',
        ]);
        $site->update($validatedData);
        return redirect('/sites');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(site $site)
    {
        $site->delete();
        return redirect('/sites');
    }
}
