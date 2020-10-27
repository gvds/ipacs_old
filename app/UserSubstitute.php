<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSubstitute extends Pivot
{
    protected $table = 'user_substitute';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function substitute_user()
    {
        return $this->belongsTo(User::class,'substitute_user_id','user_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

}
