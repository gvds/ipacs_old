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
    'tubeLabelType_id',
    'storageSampleType',
    'parentSampleType_id'
  ];

  public function event_samples()
  {
    return $this->hasMany(event_sample::class);
  }

  public function tubeLabelType()
  {
    return $this->belongsTo(tubeLabelType::class,'tubeLabelType_id','id');
  }

  public function parentSampleType()
  {
    return $this->belongsTo(sampletype::class, 'parentSampleType_id', 'id');
  }
  
}
