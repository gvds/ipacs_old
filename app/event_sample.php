<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

    public function logAsTransferred()
    {
        $this->samplestatus_id = 7;
        $this->update();
    }

    public function logAsReceived()
    {
        $response = $this->update(['samplestatus_id' => 10]);
        if (!$response) {
            throw new Exception("Error updating sample $this->barcode status as received", 1);
        }
    }

    public function logIntoSite(int $site)
    {
        if ($this->location) {
            $location = \App\location::find($this->location);
            $location->freelocation();
        }
        $response = $this->update([
            'samplestatus_id' => 3,
            'site_id' => $site,
            'location' => null
        ]);
        if (!$response) {
            throw new Exception("Error logging sample $this->barcode into local site", 1);
        }
    }

    public function returnToSource()
    {
        $response = $this->update([
            'samplestatus_id' => 3
        ]);
        if (!$response) {
            throw new Exception("Error returning sample $this->barcode to source site", 1);
        }
    }

    public function unlog()
    {
        if ($this->location and in_array($this->samplestatus_id, [3, 9])) {
            $location = \App\location::find($this->location);
            $location->freelocation();
        }
        $this->delete();
    }
}
