<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class virtualUnit extends Model
{
    protected $table = 'virtualUnits';

    protected $guarded = [];

    public function physicalUnit()
    {
        return $this->belongsTo(physicalUnit::class, 'physicalUnit_id', 'id');
    }

    public function locations()
    {
        return $this->hasMany(location::class, 'virtualUnit_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(project::class);
    }

    public function consolidate()
    {
        if (($gaplocations = $this->gaplocations)->count() === 0) {
            throw new Exception("There are no gaps in this virtual unit", 1);
        }
        $usedlocations = $this->usedlocations;
        $relocations = [];
        // while ($gaplocations->count() > 0 & $usedlocations->count() > 0 & $usedlocations->last()?->id > $gaplocations->last()?->id) {
        while ($gaplocations->count() > 0 & $usedlocations->count() > 0 & ($usedlocations->last() ? $usedlocations->last()->id : null) > ($gaplocations->last() ? $gaplocations->last()->id : null)) {
            $gaplocation = $gaplocations->shift();
            $usedlocation = $usedlocations->pop();
            $relocations[$gaplocation->id] = clone $usedlocation;
            $gaplocation->update([
                'used' => 1,
                'virgin' => 1,
                'barcode' => $relocations[$gaplocation->id]->barcode
            ]);
            $usedlocation->update([
                'used' => 0,
                'virgin' => 1,
                'barcode' => null
            ]);
        }
        $samples = event_sample::whereIn('location', Arr::pluck($relocations, 'id'))
            ->where('samplestatus_id', 3)
            ->get()->keyBy('location');
        $consolidation = Storageconsolidation::create([
            'user_id' => Auth::user()->id,
            'virtualunit_id' => $this->id
        ]);
        foreach ($relocations as $location_id => $relocated) {
            $samples[$relocated->id]->location = $location_id;
            $consolidation->addRelocation($relocated->barcode, $relocated->id, $location_id);
        }
        foreach ($samples as $key => $sample) {
            $sample->update();
        }
    }

    public function gaplocations()
    {
        $lastused = $this->lastused();
        return $this->locations()
            ->where('used', 0)
            ->where('id', '<', ($lastused ? $lastused->id : 0))
            // ->where('id', '<', ($lastused?->id ?? 0))
            ->orderBy('id');
    }

    public function usedlocations()
    {
        return $this->locations()
            ->where('used', 1)
            ->orderBy('id');
    }

    public function lastused()
    {
        return $this->locations()
            ->where('used', 1)
            ->orderBy('id', 'desc')
            ->first();
    }
}
