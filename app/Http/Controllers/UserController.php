<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('firstname')
            ->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
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
            'username' => 'required|between:3,50|unique:users,username',
            'firstname' => 'required|between:2,50',
            'surname' => 'required|between:2,50',
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|regex:/^0\d{2} \d{3}-\d{4}$/',
            'homesite' => 'required|between:3,20'
        ]);
        $user = User::create(array_merge($validatedData, ['password' => bcrypt(Str::random(30))]));

        Mail::to($user->email)
            ->queue(new NewAccount($user->firstname, 'Your new IPACS account has been created'));

        return redirect('/user');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function editroles(User $user)
    {
        $userroles = $user->roles->pluck('name', 'id')->toArray();
        $loginuser = \Auth::user();
        if ($loginuser->hasRole('sysadmin')) {
            $roles = \App\Role::pluck('name', 'id');
            $roles_restricted = collect([]);
        } else {
            $roles = \App\Role::where('restricted', false)->pluck('name', 'id');
            $roles_restricted = \App\Role::where('restricted', true)->pluck('name', 'id');
        }
        return view('users.roles', compact('user', 'roles', 'roles_restricted', 'userroles'));
    }

    public function updateroles(Request $request, User $user)
    {
        $roles = array_keys($request->all());
        array_shift($roles);
        $loginuser = \Auth::user();
        if (!$loginuser->hasRole('sysadmin')) { // Preventing non-sysadmin user from assigning or removing restricted roles
            $restrictedRoles = \App\Role::where('restricted', true)->pluck('name')->toArray();
            $userRestricted = $user->roles->where('restricted', true)->pluck('name')->toArray();
            $rolesUnrestricted = array_diff($roles, $restrictedRoles);
            $roles = array_merge($rolesUnrestricted, $userRestricted);
        }
        $user->syncRoles($roles);
        return redirect('/user');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'username' => 'required|between:3,50|unique:users,username,' . $user->id,
            'firstname' => 'required|between:2,50',
            'surname' => 'required|between:2,50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|regex:/^0\d{2} \d{3}-\d{4}$/',
            'homesite' => 'required|between:3,20'
        ]);
        $user->update($validatedData);
        return redirect('/user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect('/user/');
    }

    public function impersonation()
    {
        if(session('currentProject') === null){
            return redirect('/')->with('error','No Project Selected');
        }
        $users = User::whereHas('teams', function($query){
            $query->where('id', session('currentProject'));
        })
        ->where('id', '!=', auth()->user()->id)
        ->orderBy('firstname')
        ->get();
        return view('users.impersonate', compact('users'));
    }

    public function user_impersonate_start(User $user)
    {
        Session::put('original_user', Auth::id());
        Auth::login($user);
        return redirect('/')->with('message','You are now impersonating ' . $user->fullname);
    }

    public function user_impersonate_stop()
    {
        $id = Session::pull('original_user');
        $original_user = User::find($id);
        Auth::login($original_user);
        return redirect('/')->with('message', 'You are now yourself again');
    }

}
