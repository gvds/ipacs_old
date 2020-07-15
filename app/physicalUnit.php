<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class physicalUnit extends Model
{
  protected $primaryKey = 'unitID';
  public $incrementing = false;
  protected $keyType = 'string';
}
