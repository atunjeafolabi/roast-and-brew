<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use File;

use App\Http\Requests\StoreCafeRequest;
use App\Http\Requests\EditCafeRequest;
use App\Models\Cafe;
use App\Utilities\GoogleMaps;
use App\Utilities\Tagger;
use App\Models\CafePhoto;
use App\Models\Company;
use \Cviebrock\EloquentSluggable\Services\SlugService;


class CafesController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getCafes()
    {
        $cafes = Cafe::with('brewMethods')
                ->with(['tags' => function( $query ){
                    $query->select('tag');
                }])
                ->with('company')
                ->withCount('userLike')
                ->where('deleted', '=', 0)
                ->get();

        return response()->json( $cafes );  //can use Laravel api resources instead
    }

    public function getCafe( $slug )
    {
        $cafe = Cafe::where('slug', '=', $slug)
                ->with('brewMethods')
                ->withCount('userLike')
                ->with('tags')
                ->with(['company' => function( $query ){
                            $query->withCount('cafes');
                }])
                ->withCount('likes')
                ->where('deleted', '=', 0)
                ->first();

        if( $cafe != null ){
            return response()->json( $cafe );
        }else{
            abort(404);
        }
    }

    public function getCafeEditData( $slug ){
        /*
            Grab the cafe with the parent of the cafe
        */
        $cafe = Cafe::where('slug', '=', $slug)
                ->with('brewMethods')
                ->withCount('userLike')
                ->with(['company' => function( $query ){
                    $query->withCount('cafes');
                }])
                ->where('deleted', '=', 0)
                ->first();
        /*
           Return the cafe queried.
       */
        return response()->json($cafe);
    }

    public function putEditCafe( $slug, EditCafeRequest $request )
    {
        $companyID = $request->get('company_id');

        if( $companyID != '' ){

            $company = Company::where('id', '=', $companyID)->first();

            if( $request->has('company_name') ){
                $company->name 				= $request->get('company_name');
            }
            if( $request->has('company_type') ){
                $company->roaster			= $request->get('company_type') == 'roaster' ? 1 : 0;
            }
            if( $request->has('website') ){
                $company->website 		= $request->get('website');
            }

            $company->logo 				= '';
            $company->description = '';

            $company->save();

        }else{

            $company = new Company();

            if( $request->has('company_name') ){
                $company->name 				= $request->get('company_name');
            }
            if( $request->has('company_type') ){
                $company->roaster			= $request->get('company_type') == 'roaster' ? 1 : 0;
            }else{
                $company->roaster 		= 0;
            }
            if( $request->has('website') ){
                $company->website 		= $request->get('website');
            }

            $company->logo 				= '';
            $company->description = '';
            $company->added_by 		= Auth::user()->id;

            $company->save();
        }

        $cafe = Cafe::where('slug', '=', $slug)->first();

        if( $request->has('address') ){
            $address = $request->get('address');
        }else{
            $address = $cafe->address;
        }
        if( $request->has('city') ){
            $city = $request->get('city');
        }else{
            $city = $cafe->city;
        }
        if( $request->has('state') ){
            $state = $request->get('state');
        }else{
            $state = $cafe->state;
        }
        if( $request->has('zip') ){
            $zip = $request->get('zip');
        }else{
            $zip = $cafe->zip;
        }
        if( $request->has('location_name') ){
            $locationName = $request->get('location_name');
        }else{
            $locationName = $cafe->location_name;
        }
        if( $request->has('brew_methods') ){
            $brewMethods 	= json_decode( $request->get('brew_methods') );
        }

        $lat = $request->get('lat') != '' ? $request->get('lat') : 0;
        $lng = $request->get('lng') != '' ? $request->get('lng') : 0;

        if( $lat == 0 && $lng == 0 ){
            //GoogleMaps services used here for development is free and limited. May return empty data after few usage
            //Hence, GoogleMaps will be a good candidate for Mocking when testing
            $coordinates = GoogleMaps::geocodeAddress( $address, $city, $state, $zip );
            $lat = $coordinates['lat'];
            $lng = $coordinates['lng'];
        }

        $cafe = Cafe::where('slug', '=', $slug)->first();

        $cafe->company_id 			= $company->id;
        $cafe->location_name 		= $locationName != null ? $locationName : '';
        $cafe->address 				= $address;
        $cafe->city 				= $city;
        $cafe->state 				= $state;
        $cafe->zip 					= $zip;
        $cafe->latitude 			= -25;  //$lat;
        $cafe->longitude 			= 90;  //$lng;
        $cafe->added_by 			= Auth::user()->id;
        $cafe->deleted              = 0;
        $cafe->tea 				    = 0;
        $cafe->matcha               = 0;

        if( $request->has('matcha') ){
            $cafe->matcha = $request->get('matcha');
        }
        if( $request->has('tea') ){
            $cafe->tea = $request->get('tea');
        }

        $cafe->save();

        if( $request->has('brew_methods') ){
            /*
            Attach the brew methods
          */
            $cafe->brewMethods()->sync( $brewMethods );
        }

        $company =  Company::where('id', '=', $company->id)
                    ->with('cafes')
                    ->first();
        /*
          Return the edited cafes as JSON
        */
        return response()->json( $company, 200);
    }

    public function postNewCafe(StoreCafeRequest $request)
    {
        $companyID = $request->get('company_id');

        if( $companyID != '' ){

            $company =  Company::where('id', '=', $companyID)
                        ->first();
        }else{

            $company = new Company();
            $company->name 		    = $request->get('company_name');
	        $company->roaster	    = $request->get('company_type') == 'roaster' ? 1 : 0;
            $company->website       = $request->get('website');
            $company->logo 			= '';
            $company->description   = '';
            $company->added_by 		= Auth::user()->id;
            $company->save();
        }

        $address 		= $request->get('address');
        $city 			= $request->get('city');
        $state 			= $request->get('state');
        $zip 			= $request->get('zip');
		$locationName   = $request->get('location_name');
		$brewMethods 	= json_decode( $request->get('brew_methods') );

        $lat = $request->get('lat') != '' ? $request->get('lat') : 0;
        $lng = $request->get('lng') != '' ? $request->get('lng') : 0;

        if( $lat == 0 && $lng == 0 ){
            //GoogleMaps services used here for development is free and limited. May return empty data after few usage
            //Hence, GoogleMaps will be a good candidate for Mocking when testing
            $coordinates = GoogleMaps::geocodeAddress( $address, $city, $state, $zip );
            $lat = $coordinates['lat'];
            $lng = $coordinates['lng'];
        }

        $cafe                   = new Cafe();
        $cafe->company_id 		= $company->id;
        $cafe->slug 			= SlugService::createSlug(Cafe::class, 'slug', $company->name.' '.$locationName.' '.$address.' '.$city.' '.$state);        $cafe->location_name 	= $locationName != null ? $locationName : '';
        $cafe->address 			= $address;
        $cafe->city 			= $city;
        $cafe->state 			= $state;
        $cafe->zip 				= $zip;
        $cafe->latitude 		= -20;  //$lat;
        $cafe->longitude 		= 85;   //$lng;
        $cafe->added_by 		= Auth::user()->id;
        $cafe->deleted 			= 0;
        $cafe->tea 				= $request->has('tea') ? $request->get('tea') : 0;
        $cafe->matcha 			= $request->has('matcha') ? $request->get('matcha') : 0;

        $cafe->save();

        $cafe->brewMethods()->sync( $brewMethods );

        //This call is redundant
        $company =  Company::where('id', '=', $company->id)
                    ->with('cafes')
                    ->first();

        return response()->json( $company, 201);
    }

    public function postLikeCafe( $slug, Request $request ){

        $cafe = Cafe::where('slug', '=', $slug)->first();

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

    public function deleteLikeCafe( $slug ){

        $cafe = Cafe::where('slug', '=', $slug)->first();

        $cafe->likes()->detach( Auth::user()->id );

        return response(null, 204);
    }

    public function postAddTags( Request $request, $slug ){

        $tags = $request->get('tags');

        $cafe = Cafe::where('slug', '=', $slug)->first();

        /*
          Tags the cafe
        */
        Tagger::tagCafe( $cafe, $tags );

        /*
          Grabs the cafe with the brew methods, user like and tags
        */
        $cafe = Cafe::where('slug', '=', $slug)
                ->with('brewMethods')
                ->with('userLike')
                ->with('tags')
                ->first();

        return response()->json($cafe, 201);
    }

    public function deleteCafeTag( $slug, $tagID ){

        DB::statement('DELETE FROM cafes_users_tags WHERE cafe_id = "'.$slug.'" AND tag_id = "'.$tagID.'" AND user_id = "'.Auth::user()->id.'"');

        /*
          Return a proper response code for successful untagging
        */
        return response(null, 204);
    }

    public function deleteCafe( $slug ){
        $cafe = Cafe::where('slug', '=', $slug)->first();
        $cafe->deleted = 1;
        $cafe->save();
        return response()->json('', 204);
    }
}
