<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitDefinition extends Model
{
  protected $primaryKey = 'unitType';
  public $incrementing = false;
  protected $keyType = 'string';
}
