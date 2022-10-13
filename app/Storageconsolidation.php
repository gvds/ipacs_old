<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;

class Storageconsolidation extends Model
{

    protected $guarded = [];

    public function relocations()
    {
        return $this->hasMany(Relocation::class);
    }

    public function virtualunit()
    {
        return $this->belongsTo(virtualUnit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addRelocation(String $barcode, Int $source_location, Int $destination_location)
    {
        $this->relocations()->create([
            'barcode' => $barcode,
            'source_location_id' => $source_location,
            'destination_location_id' => $destination_location
        ]);
    }

    public function generateReport()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="samplelist.csv"',
        ];
        $data = "Project\t" . $this->virtualunit->project . "\n";
        $data .= "Virtual Unit\t" . $this->virtualunit->virtualUnit . "\n";
        $data .= "Physical Unit\t" . $this->virtualunit->physicalUnit->unitID . "\n";
        $data .= "User\t" . $this->user->fullname . "\n";
        $data .= "Date\t" . Carbon::parse($this->created_at)->toDateString() . "\n";
        $data .= "Barcode\tSource\tDestination\n";
        foreach ($this->relocations->load('sourcelocation', 'destinationlocation') as $relocation) {
            $relocationdata = [
                $relocation->barcode,
                $relocation->sourcelocation->rack . ':' . $relocation->sourcelocation->box  . ':' . $relocation->sourcelocation->position,
                $relocation->destinationlocation->rack  . ':' . $relocation->destinationlocation->box  . ':' . $relocation->destinationlocation->position,
            ];
            $data .= implode("\t", $relocationdata) . "\n";
        }
        return Response::make($data, 200, $headers);
    }
}
