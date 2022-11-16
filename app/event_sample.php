<?php

namespace App;

use App\Events\event_sampleUpdated;
use Exception;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Route;

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

    public function updateVolume($volume)
    {
        $this->volume = $volume;
        $this->save();
        $this->logChange(__FUNCTION__);
    }

    public function logOut()
    {
        $this->samplestatus_id = 9;
        $this->loggedOutBy = auth()->user()->id;
        $this->update();
        $this->logChange(__FUNCTION__);
    }

    public function logReturn()
    {
        $this->thawcount += 1;
        $this->samplestatus_id = 3;
        $this->update();
        $this->logChange(__FUNCTION__);
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
        $this->logChange(__FUNCTION__);
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
        $this->logChange(__FUNCTION__);
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

    public function logIntoSite(int $site_id)
    {
        if ($this->location) {
            $location = \App\location::find($this->location);
            $location->freelocation();
        }
        $response = $this->update([
            'samplestatus_id' => 3,
            'site_id' => $site_id,
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
        $this->logChange(__FUNCTION__);
    }

    public function logChange($action)
    {
        samplelog::create([
            'event_sample_id' => $this->id,
            'user_id' => auth()->user()->id,
            'action' => $action,
            'detail' => json_encode($this)
        ]);
    }
}
