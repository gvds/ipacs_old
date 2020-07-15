<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhysicalUnitController extends Controller
{
  protected $primaryKey = 'unitID';
  public $incrementing = false;
  protected $keyType = 'string';
}
