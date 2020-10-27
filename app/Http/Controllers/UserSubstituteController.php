<?php

namespace App\Http\Controllers;

use App\User;
use App\UserSubstitute;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSubstituteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $candidateSubstitutes = \App\Team::find(session('currentProject'))
            ->subject_managers()
            ->where('id', '!=', auth()->user()->id)
            ->where('site_id', auth()->user()->ProjectSite)
            ->get();

        // $currentSubstitute = auth()->user()->substitute()->get();
        $currentSubstitute = Auth::user()->substitute->first();
        
        return view('userSubstitutes.index', compact('currentSubstitute', 'candidateSubstitutes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $user->substitute()->sync([$request->substitute_id => ['team_id' => session('currentProject')]]);
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
    public function show(UserSubstitute $userSubstitute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserSubstitute  $userSubstitute
     * @return \Illuminate\Http\Response
     */
    public function edit(UserSubstitute $userSubstitute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserSubstitute  $userSubstitute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserSubstitute $userSubstitute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserSubstitute  $userSubstitute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $user = auth()->user();
            // $user->substitute()->detach($request->substitute_id, ['team_id' => session('currentProject')]);
            $user->substitute()->detach();
        } catch (\Throwable $th) {
            return back()->with('error', 'Substitution Deactivation Failed: ' . $th->getMessage());
        }
        return back()->with('message', 'Substitution Deactivated');
    }
}
