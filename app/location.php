<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class location extends Model
{

  public function virtualUnit()
  {
    return $this->belongsTo(virtualUnit::class, 'virtualUnit_id', 'id');
  }
  
}
