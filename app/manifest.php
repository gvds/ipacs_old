<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class manifest extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receiver()
    {
        return $this->belongsTo(user::class,'received_user_id','id');
    }

    public function destination()
    {
        return $this->belongsTo(site::class,'destinationSite_id','id');
    }

    public function source()
    {
        return $this->belongsTo(site::class,'sourceSite_id','id');
    }
}
