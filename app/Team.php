<?php

namespace App;

use Laratrust\Models\LaratrustTeam;

class Team extends LaratrustTeam
{
    public $incrementing = false;
    
    public $guarded = [];

    // public function permission_users()
    // {
    //     return $this->belongsToMany(User::class, 'permission_user');
    // }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps()
        ->withPivot('site_id','redcap_api_token');
    }

    public function subject_managers()
    {
        return $this->belongsToMany(User::class)
        ->wherePermissionIs('manage-subjects');
    }

    public function sites()
    {
        return $this->hasMany(site::class,'project_id');
    }

}
