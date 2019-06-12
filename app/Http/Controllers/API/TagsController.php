<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Tag;

class TagsController extends Controller
{
    public function getTags(){

        $query = Request::get('search');

        /*
          If the query is not set or is empty, load all the tags.
          Otherwise load the tags that match the query
        */
        if( $query == null || $query == '' ){
            $tags = Tag::all();
        }else{
            $tags = Tag::where('tag', 'LIKE', $query.'%')->get();
        }

        return response()->json( $tags );
    }
}
