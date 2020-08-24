<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class event_subject extends Pivot
{

    public function subject()
    {
        return $this->belongsTo(subject::class);
    }

    public function event()
    {
        return $this->belongsTo(event::class);
    }
    
    public function status()
    {
        return $this->belongsTo(eventStatus::class,'eventstatus_id','id');
    }

}
