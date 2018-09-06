<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Login extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public $relationships = ['userId','roleId'];
    protected $guarded = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];
    protected $hidden = ['password', 'remember_token',];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Login','created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Login', 'updated_by');
    }

    public function userId()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function roleId()
    {
        return $this->belongsTo('App\Role', 'role_id');
    }

}
