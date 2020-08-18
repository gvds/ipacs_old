<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class project extends Model
{
  protected $fillable = [
    'project',
    'redcapProject_id',
    'owner',
    'subject_id_prefix',
    'subject_id_digits',
    'storageProjectName',
    'label_id',
    'last_subject_id'
  ];

  public function projectOwner()
  {
    return $this->belongsTo(User::class, 'owner', 'id');
  }

  public function team()
  {
    return $this->hasOne(Team::class, 'id', 'id');
  }

  public function arms()
  {
    return $this->hasMany(arm::class);
  }
  
  public function events()
  {
    return $this->hasManyThrough(event::class, arm::class);
  }

  public function sampletypes()
  {
    return $this->hasMany(sampletype::class);
  }

}
