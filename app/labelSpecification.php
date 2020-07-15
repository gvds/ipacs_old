<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class labelSpecification extends Model
{
  protected $primaryKey = 'label_id';
  public $incrementing = false;
  protected $keyType = 'string';
}
