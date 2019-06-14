<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

use App\Http\Requests\StoreCafeRequest;
use App\Models\Cafe;
use App\Utilities\GoogleMaps;

class CafesController extends Controller
{
    public function getCafes()
    {
        $cafes = Cafe::with('brewMethods')
                ->with(['tags' => function( $query ){
                    $query->select('tag');
                }])
                ->get();

        return response()->json( $cafes );  //can use Laravel api resources instead
    }

    public function getCafe( $id )
    {
        $cafe = Cafe::where('id', '=', $id)
                ->with('brewMethods')
                ->with('userLike')
                ->with('tags')
                ->first();

        return response()->json( $cafe );
    }

    public function postNewCafe(StoreCafeRequest $request)
    {
        $addedCafes = array();

        $locations = $request->get('locations');

//        var_dump($request->all());exit;

        /*
          Create a parent cafe and grab the first location
        */
        $parentCafe = new Cafe();

        $address            = $locations[0]['address'];
        $city                   = $locations[0]['city'];
        $state                  = $locations[0]['state'];
        $zip                        = $locations[0]['zip'];
        $locationName       = $locations[0]['name'];
        $brewMethods        = $locations[0]['methodsAvailable'];
        $tags = $locations[0]['tags'];

        /*
          Get the Latitude and Longitude returned from the Google Maps Address.
        */
//        $coordinates = GoogleMaps::geocodeAddress( $address, $city, $state, $zip );
//        var_dump($coordinates);exit;

        $parentCafe->name                   = $request->get('name');
        $parentCafe->location_name  = $locationName != '' ? $locationName : '';
        $parentCafe->address                = $address;
        $parentCafe->city                   = $city;
        $parentCafe->state                  = $state;
        $parentCafe->zip                        = $zip;
        $parentCafe->latitude           = 35.0;//$coordinates['lat'];
        $parentCafe->longitude          = 35.0;//$coordinates['lng'];
        $parentCafe->roaster                = $request->get('roaster') != '' ? 1 : 0;
        $parentCafe->website                = $request->get('website');
        $parentCafe->description        = $request->get('description') != '' ? $request->get('description') : '';
        $parentCafe->added_by           = Auth::user()->id;

        $parentCafe->save();

        /*
          Attach the brew methods
        */
        $parentCafe->brewMethods()->sync( $brewMethods );

        Tagger::tagCafe( $parentCafe, $tags );

        array_push( $addedCafes, $parentCafe->toArray() );

        /*
          Now that we have the parent cafe, we add all of the other
          locations. We have to see if other locations are added.
        */
        if( count( $locations ) > 1 ){
            /*
              We off set the counter at 1 since we already used the
              first location.
            */
            for( $i = 1; $i < count( $locations ); $i++ ){
                /*
                  Create a cafe and grab the location
                */
                $cafe = new Cafe();

                $address            = $locations[$i]['address'];
                $city                   = 35.0;//$locations[$i]['city'];
                $state                  = 35.0;//$locations[$i]['state'];
                $zip                        = $locations[$i]['zip'];
                $locationName       = $locations[$i]['name'];
                $brewMethods        = $locations[$i]['methodsAvailable'];
                $tags               = $locations[$i]['tags'];

                /*
                  Get the Latitude and Longitude returned from the Google Maps Address.
                */
//                $coordinates = GoogleMaps::geocodeAddress( $address, $city, $state, $zip );

                $cafe->parent               = $parentCafe->id;
                $cafe->name                     = $request->get('name');
                $cafe->location_name    = $locationName != '' ? $locationName : '';
                $cafe->address              = $address;
                $cafe->city                     = $city;
                $cafe->state                    = $state;
                $cafe->zip                      = $zip;
                $cafe->latitude             = 20.00;//$coordinates['lat'];
                $cafe->longitude            = 20.00;//$coordinates['lng'];
                $cafe->roaster              = $request->get('roaster') != '' ? 1 : 0;
                $cafe->website              = $request->get('website');
                $cafe->description      = $request->get('description') != '' ? $request->get('description') : '';
                $cafe->added_by             = Auth::user()->id;

                $cafe->save();

                /*
                  Attach the brew methods
                */
                $cafe->brewMethods()->sync( $brewMethods );

                Tagger::tagCafe( $cafe, $tags );

                array_push( $addedCafes, $cafe->toArray() );
            }
        }

        return response()->json($addedCafes, 201);
    }

    public function postLikeCafe( $cafeID ){

        $cafe = Cafe::where('id', '=', $cafeID)->first();

        /*
            If the user doesn't already like the cafe, attaches the cafe to the user's likes
        */
        if( !$cafe->likes->contains( Auth::user()->id ) ) {

            $cafe->likes()->attach(Auth::user()->id, [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return response()->json( ['cafe_liked' => true], 201 );
    }

    public function deleteLikeCafe( $cafeID ){

        $cafe = Cafe::where('id', '=', $cafeID)->first();

        $cafe->likes()->detach( Auth::user()->id );

        return response(null, 204);
    }

    public function postAddTags( $cafeID ){

        $tags = Request::get('tags');

        $cafe = Cafe::where('id', '=', $cafeID)->first();

        /*
          Tags the cafe
        */
        Tagger::tagCafe( $cafe, $tags );

        /*
          Grabs the cafe with the brew methods, user like and tags
        */
        $cafe = Cafe::where('id', '=', $cafeID)
                ->with('brewMethods')
                ->with('userLike')
                ->with('tags')
                ->first();

        return response()->json($cafe, 201);
    }

    public function deleteCafeTag( $cafeID, $tagID ){

        DB::statement('DELETE FROM cafes_users_tags WHERE cafe_id = `'.$cafeID.'` AND tag_id = `'.$tagID.'` AND user_id = `'.Auth::user()->id.'`');

        /*
          Return a proper response code for successful untagging
        */
        return response(null, 204);
    }
}
