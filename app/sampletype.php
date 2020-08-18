<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sampletype extends Model
{
  protected $table = 'samples';
  
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
  // protected $fillable = [
  //   'barcode',
  //   'event_id',
  //   'site',
  //   'sampletype_id',
  //   'samplestatus_id',
  //   'location',
  //   'labelType',
  //   'volume',
  //   'volumeUnit',
  //   'loggedBy',
  //   'logTime',
  //   'usedBy',
  //   'usedTime',
  //   'aliquot',
  //   'parentBarcode'
  // ];
  public function event_samples()
  {
    return $this->hasMany(event_sample::class);
  }
}
