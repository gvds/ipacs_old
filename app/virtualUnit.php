<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class virtualUnit extends Model
{
    protected $table = 'virtualUnits';

    protected $guarded = [];

    public function physicalUnit()
    {
        return $this->belongsTo(physicalUnit::class, 'physicalUnit_id', 'id');
    }
    
}
