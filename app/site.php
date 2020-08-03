<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class site extends Model
{
    protected $fillable =[
        'name',
        'project_id'
    ];

    protected $appends = [
        'site',
    ];
    
    public function getLabelAttribute()
    {
       return trans('site'.$this->name);
    }
}
