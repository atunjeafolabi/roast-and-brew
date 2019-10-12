<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'provider', 'provider_id'
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

    public function likes(){
        return $this->belongsToMany( 'App\Models\Cafe', 'users_cafes_likes', 'user_id', 'cafe_id');
    }

    public function cafePhotos(){
        return $this->hasMany( 'App\Models\CafePhoto', 'id', 'cafe_id' );
    }

    public function actions(){
        return $this->hasMany( 'App\Models\Action', 'id', 'user_id' );
    }

    public function actionsProcessed(){
        return $this->hasMany( 'App\Models\Action', 'id', 'processed_by' );
    }

    public function companiesOwned(){
        return $this->belongsToMany( 'App\Models\Company', 'companies_owners', 'user_id', 'company_id' );
    }
}
