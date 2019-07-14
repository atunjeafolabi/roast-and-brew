<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CafeAction extends Model
{
    protected $table = 'cafes_actions';

    /**
     * An action belongs to a cafe.
     */
    public function cafe(){
        return $this->belongsTo( 'App\Models\Cafe', 'cafe_id', 'id' );
    }

    /**
     * An action is performed by a user
     */
    public function by(){
        return $this->belongsTo( 'App\Models\User', 'user_id', 'id' );
    }

    /**
     * An action is processed by a user.
     */
    public function processedBy(){
        return $this->belongsTo( 'App\Models\User', 'processed_by', 'id' );
    }
}