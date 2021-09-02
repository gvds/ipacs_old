<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class storageReport extends Model
{

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function storageLogs()
  {
    return $this->hasMany(storageLog::class,'storageReport_id','id')->with('sample');
  }
}
