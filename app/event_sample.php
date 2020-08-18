<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class event_sample extends Pivot
{
    protected $table = 'event_sample';

    public function event_subject()
    {
        return $this->belongsTo('event_subject','event_subject_id','id');
    }

    public function sample()
    {
        return $this->belongsTo(sample::class,'sample_id','id');
    }
}
