<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class storageLog extends Model
{
    public function sampletype()
    {
        return $this->belongsTo(sampletype::class);
    }

    public function sample()
    {
        return $this->belongsTo(event_sample::class,'sample_id','id');
    }

    public function storageLocation()
    {
        return $this->belongsTo(location::class);
    }
}
