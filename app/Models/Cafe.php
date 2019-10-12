<?php

namespace App\Models;

use Request;
use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\Sluggable;


class Cafe extends Model
{
    protected $fillable = ['name', 'address', 'city', 'state', 'zip', 'latitude', 'longitude'];

    use Sluggable;

    /**
     * Defines the sluggable implementation.
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['company.name', 'location_name', 'address', 'city', 'state']
            ]
        ];
    }

    /**
     * A cafe can have many brew methods.
     */
    public function brewMethods()
    {
        return $this->belongsToMany(
            'App\Models\BrewMethod', 'cafes_brew_methods',
            'cafe_id', 'brew_method_id'
        );
    }

//    public function children(){
//        return $this->hasMany( 'App\Models\Cafe', 'parent', 'id' );
//    }
//
//    public function parent(){
//        return $this->hasOne( 'App\Models\Cafe', 'id', 'parent' );
//    }

    /**
     * A cafe belongs to one company.
     */
    public function company()
    {
//        return $this->hasOne('App\Models\Company', 'id', 'company');
        return $this->belongsTo( 'App\Models\Company', 'company_id', 'id' );

    }

    /**
     * A cafe can have many user likes.
     */
    public function likes()
    {
        return $this->belongsToMany( 'App\Models\User', 'users_cafes_likes', 'cafe_id', 'user_id');
    }

    /**
     * A cafe can have one like from a specific user.
     */
    public function userLike()
    {
        $userID = Request::user('api') != '' ? Request::user('api')->id : null;

        return $this->belongsToMany( 'App\Models\User', 'users_cafes_likes', 'cafe_id', 'user_id')
                    ->where('user_id', $userID );

//        return $this->belongsToMany( 'App\Models\User', 'users_cafes_likes', 'cafe_id', 'user_id')->where('user_id', auth()->id());
    }

    /**
     * A cafe can have many tags.
     */
    public function tags()
    {
        return $this->belongsToMany( 'App\Models\Tag', 'cafes_users_tags', 'cafe_id', 'tag_id');
    }

    /**
     * A cafe can have many photos.
     */
    public function photos()
    {
        return $this->hasMany( 'App\Models\CafePhoto', 'id', 'cafe_id' );
    }

    /**
     * A cafe can have many actions
     */
    public function actions()
    {
        return $this->hasMany( 'App\Models\CafeAction', 'id', 'cafe_id' );
    }
}
