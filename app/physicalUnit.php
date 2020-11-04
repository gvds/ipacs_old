<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class physicalUnit extends Model
{
    protected $table = 'physicalUnits';

    protected $guarded = [];

    public function administrator()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function virtualUnits()
    {
        return $this->hasMany(virtualUnit::class, 'physicalUnit_id', 'id');
    }

    public function unitType()
    {
        return $this->belongsTo(unitDefinition::class, 'unitDefinition_id', 'id');
    }

}
