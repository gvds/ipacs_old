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
    $permissions = Permission::orderBy('name')->get();
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
      'name' => 'required|unique:permissions|between:3,50'
    ]);
    Permission::create($request->all());
    return redirect('/permissions');
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
      'name' => 'required|between:3,50|unique:permissions,name,'.$permission->id
    ]);
    $permission->update($request->all());
    return redirect('/permissions');
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy(Permission $permission) {
    $permission->delete();
    return redirect('/permissions');
  }
}
