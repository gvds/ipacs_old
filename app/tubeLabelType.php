<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tubeLabelType extends Model
{
    protected $fillable = [
        'tubeLabelType',
        'preregister',
        'registration',
        'barcodeFormat',
        'project_id'
    ];
}
