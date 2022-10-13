<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relocation extends Model
{

    protected $guarded = [];

    public function sourcelocation()
    {
        return $this->belongsTo(location::class, 'source_location_id', 'id');
    }

    public function destinationlocation()
    {
        return $this->belongsTo(location::class, 'destination_location_id', 'id');
    }
}
