<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class event_sample extends Pivot
{
    protected $table = 'event_sample';

    public function event_subject()
    {
        return $this->belongsTo('event_subject', 'event_subject_id', 'id');
    }

    public function sampletype()
    {
        return $this->belongsTo(sampletype::class, 'sampletype_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(sampleStatus::class, 'samplestatus_id', 'id');
    }

    public function derivativeCount()
    {
        return event_sample::where('parentBarcode',$this->barcode)->count();
    }

    public function sampleActor($user_id)
    {
        return \App\User::find($user_id);
    }
}
