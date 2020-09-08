<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class location extends Model
{
  protected $primaryKey = 'location_id';

  public function virtualUnit()
  {
    return $this->belongsTo(virtualUnit::class, 'virtualUnit_id', 'virtualUnit_id');
  }
}
