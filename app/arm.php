<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class arm extends Model
{
    protected $fillable = [
        'name',
        'project_id',
        'redcap_arm_id',
        'arm_num',
        'manual_enrol',
        'switcharms'
    ];

    public function events()
    {
        return $this->hasMany(event::class)->orderBy('offset');
    }
}
