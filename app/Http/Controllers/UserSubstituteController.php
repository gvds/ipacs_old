<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSubstituteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $substitutableUsers = \App\Team::find(session('currentProject'))
            ->subject_managers()
            ->where(function ($query) use ($request) {
                if (!(Auth::user()->isAbleTo('manage-teams', $request->currentProject->team->name) or Auth::user()->owns($request->currentProject, 'owner'))) {
                    $query->where('site_id', Auth::user()->project_site);
                }
            })
            ->with('currentSite')
            ->orderBy('site_id')
            ->orderBy('firstname')
            ->get();
        return view('userSubstitutes.index', compact('substitutableUsers'));
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
            'user_id' => 'required|exists:users,id',
            'substitute_id' => 'required|exists:users,id'
        ]);
        if (!$this->checkPermissions(intval($validatedData['user_id']), $request->currentProject)) {
            return back()->with('error', 'You do not have permission to edit that user');
        }
        try {
            $user = User::find($validatedData['user_id']);
            $user->substitute()->sync([$validatedData['substitute_id'] => ['team_id' => session('currentProject')]]);
        } catch (\Throwable $th) {
            return back()->with('error', 'Substitution Failed: ' . $th->getMessage());
        }
        return back()->with('message', 'Substitution Effected');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserSubstitute  $userSubstitute
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user)
    {
        if (!$this->checkPermissions($user->id, $request->currentProject)) {
            return back()->with('error', 'You do not have permission to edit that user');
        }
        $candidateSubstitutes = \App\Team::find(session('currentProject'))
            ->subject_managers()
            ->where('id', '!=', $user->id)
            ->where('site_id', $user->project_site)
            ->get();
        $currentSubstitute = $user->substitute->first();
        return view('userSubstitutes.show', compact('user', 'currentSubstitute', 'candidateSubstitutes'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserSubstitute  $userSubstitute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        if (!$this->checkPermissions(intval($validatedData['user_id']), $request->currentProject)) {
            return back()->with('error', 'You do not have permission to edit that user');
        }
        try {
            $user = User::find($validatedData['user_id']);
            $user->substitute()->detach();
        } catch (\Throwable $th) {
            return back()->with('error', 'Substitution Deactivation Failed: ' . $th->getMessage());
        }
        return back()->with('message', 'Substitution Deactivated');
    }

    private function checkPermissions(int $user_id, \app\project $currentProject)
    {
        if ($user_id === Auth::user()->id or Auth::user()->isAbleTo('manage-teams', $currentProject->team->name) or Auth::user()->owns($currentProject, 'owner')) {
            return true;
        }
        return false;
    }
}
