<?php

namespace App\Http\Controllers;

use App\Role;
use \App\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
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
    $roles = Role::orderBy('name')
      ->get();
    return view('roles.index', compact('roles'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('roles.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|unique:roles|between:3,50',
      'display_name' => 'required|max:50',
      'description' => 'nullable|max:100',
      'restricted' => 'required|boolean'
    ]);
    Role::create($request->all());
    return redirect('/roles');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function show(Role $role)
  {
    $rolepermissions = $role->permissions->pluck('name', 'id')->toArray();
    $permissions = \App\Permission::where('scope', 'system')->pluck('name', 'id');
    return view('roles.show', compact('role', 'permissions', 'rolepermissions'));
  }

  public function updatepermissions(Request $request, Role $role)
  {
    $permissions = array_keys($request->all());
    array_shift($permissions);
    $role->permissions()->sync($permissions);
    return redirect('/roles');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function edit(Role $role)
  {
    return view('roles.edit', compact('role'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Role $role)
  {
    $request->validate([
      'name' => 'required|between:3,50|unique:roles,name,' . $role->id,
      'display_name' => 'required|max:50',
      'description' => 'nullable|max:100',
      'restricted' => 'required|boolean'
    ]);
    $role->update($request->all());
    return redirect('/roles');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function destroy(Role $role)
  {
    $role->delete();
    return redirect('/roles');
  }
}
