<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class datafile extends Model
{
    protected $fillable = [
        'user',
        'site',
        'fileset',
        'resource',
        'filename',
        'generationDate',
        'lab',
        'platform',
        'opperator',
        'description',
        'hash',
        'filesize',
        'filetype',
        'software',
        'owner'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(site::class);
    }
}
