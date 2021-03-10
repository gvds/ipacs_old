<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Permission;

class PermissionController extends Controller {

  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index() {
    $permissions = Permission::orderBy('scope','desc')->orderBy('name')->get();
    return view('permissions.index', compact('permissions'));
  }

  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function create() {
    return view('permissions.create');
  }

  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request) {
    $request->validate([
      'name' => 'required|unique:permissions|between:3,50',
      'display_name' => 'required|max:50',
      'scope' => 'required|in:system,project',
      'description' => 'nullable|max:100'
    ]);
    Permission::create($request->all());
    return redirect('/permission');
  }

  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id)
  {
    //
  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function edit(Permission $permission) {
    return view('permissions.edit', compact('permission'));
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request, Permission $permission) {
    $request->validate([
      'name' => 'required|between:3,50|unique:permissions,name,'.$permission->id,
      'display_name' => 'required|max:50',
      'scope' => 'required|in:system,project',
      'description' => 'nullable|max:100'
    ]);
    $permission->update($request->all());
    return redirect('/permission');
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy(Permission $permission) {
    $permission->delete();
    return redirect('/permission');
  }
}
