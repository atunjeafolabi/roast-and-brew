<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCafeRequest;
use Auth;
use App\Models\Cafe;
use App\Utilities\GoogleMaps;

class CafesController extends Controller
{
    public function getCafes()
    {
        $cafes = Cafe::with('brewMethods')->get();

        return response()->json( $cafes );  //use api resources instead
    }

    public function getCafe( $id )
    {
        $cafe = Cafe::where('id', '=', $id)
                ->with('brewMethods')
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

                /*
                  Get the Latitude and Longitude returned from the Google Maps Address.
                */
                $coordinates = GoogleMaps::geocodeAddress( $address, $city, $state, $zip );

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

                array_push( $addedCafes, $cafe->toArray() );
            }
        }

        return response()->json($addedCafes, 201);
    }
}
