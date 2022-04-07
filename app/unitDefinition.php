<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class unitDefinition extends Model
{
    protected $table = 'unitDefinitions';

    protected $guarded = [];

    public function sections()
    {
        return $this->hasMany(section::class, 'unitDefinition_id', 'id')->orderBy('section');
    }

    public function physicalUnits()
    {
        return $this->hasMany(physicalUnit::class, 'unitDefinition_id', 'id')->orderBy('unitID');
    }
}
