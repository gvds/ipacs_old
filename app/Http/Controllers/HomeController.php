<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $user = auth()->user();
        $currentProject = \App\project::find(session('currentProject', null));
        $currentSubstitute = auth()->user()->substitute->first();
        $currentSubstitutees = auth()->user()->substitutees;

        return view('home', compact('currentProject', 'currentSubstitute', 'currentSubstitutees'));
    }
}
