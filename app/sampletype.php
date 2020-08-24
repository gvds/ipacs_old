<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sampletype extends Model
{

  protected $fillable = [
    'name',
    'project_id',
    'primary',
    'aliquots',
    'pooled',
    'defaultVolume',
    'volumeUnit',
    'tranferDestination',
    'transferSource',
    'sampleGroup',
    'tubeLabelType',
    'storageSampleType',
    'parentSampleType_id'
  ];

  public function event_samples()
  {
    return $this->hasMany(event_sample::class);
  }
}
