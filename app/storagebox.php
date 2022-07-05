<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class storagebox extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sampletype()
    {
        return $this->belongsTo(sampletype::class);
    }

    public function boxPositions()
    {
        return $this->hasMany(storageboxposition::class);
    }

    public function usedPositions()
    {
        return $this->boxPositions->where('used', true);
    }
}
