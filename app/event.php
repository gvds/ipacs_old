<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class event extends Model
{
  protected $fillable = [
    'name',
    'arm_id',
    'redcap_event_id',
    'autolog',
    'offset',
    'offset_ante_window',
    'offset_post_window',
    'name_labels',
    'subject_event_labels',
    'study_id_labels',
    'active'
  ];

  public function arm()
  {
    return $this->belongsTo(arm::class);
  }

}
