<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'surname',
        'username',
        'email',
        'password',
        'telephone',
        'homesite',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->surname;
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('site_id', 'redcap_api_token');
    }

    public function team_member_permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot('team_id');
    }

    public function sites()
    {
        return $this->belongsToMany(site::class, 'team_user');
    }

    public function currentSite()
    {
        return $this->belongsToMany(site::class, 'team_user', 'user_id', 'site_id', 'id', 'id')
            ->wherePivot('team_id', session('currentProject'));
    }

    public function substitute()
    {
        return $this->belongsToMany(User::class, 'user_substitute', 'user_id', 'substitute_user_id')
            ->withTimestamps()
            ->withPivot('team_id')
            ->wherePivot('team_id', session('currentProject')); //->using(UserSubstitute::class);
    }

    public function substitutees()
    {
        return $this->belongsToMany(User::class, 'user_substitute', 'substitute_user_id', 'user_id')
            ->withTimestamps()
            ->withPivot('team_id')
            ->wherePivot('team_id', session('currentProject'));
    }

    public function getProjectSiteAttribute()
    {
        $project_id = session('currentProject');
        $site = DB::table('team_user')
            ->where('user_id', $this->id)
            ->where('team_id', $project_id)
            ->pluck('site_id')
            ->first();
        return $site;
    }
}
