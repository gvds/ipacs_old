<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class event_sample extends Pivot
{
    protected $table = 'event_sample';

    public function event_subject()
    {
        return $this->belongsTo(event_subject::class, 'event_subject_id', 'id');
    }

    public function sampletype()
    {
        return $this->belongsTo(sampletype::class, 'sampletype_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(sampleStatus::class, 'samplestatus_id', 'id');
    }

    public function site()
    {
        return $this->belongsTo(site::class);
    }

    public function storagelocation()
    {
        return $this->belongsTo(location::class, 'location', 'id');
    }

    public function derivativeCount()
    {
        return event_sample::where('parentBarcode', $this->barcode)->count();
    }

    public function sampleActor($user_id)
    {
        return \App\User::find($user_id);
    }

    public function logAsUsed()
    {
        if (in_array($this->samplestatus_id, [3, 9])) {
            $location = \App\location::find($this->location);
            $location->freelocation();
            $this->location = null;
        }
        $this->samplestatus_id = 5;
        $this->update();
    }

    public function logAsLost()
    {
        if (in_array($this->samplestatus_id, [3, 9])) {
            $location = \App\location::find($this->location);
            $location->freelocation();
            $this->location = null;
        }
        $this->samplestatus_id = 8;
        $this->update();
    }

}
