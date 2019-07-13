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
use App\Models\CafeAction;
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
        /*
            Grab the cafe to be edited.
        */
        $cafe = Cafe::where('slug', '=', $slug)->first();

        /*
			Confirms user can edit the cafe through the Cafes Policy
		*/
        if (Auth::user()->can('update', $cafe)) {
            /*
                Get the company ID to check and see if the company
                exists.
            */
            $companyID = $request->get('company_id');

            /*
				Set the before cafe to the data that was existing,
				and the after to what was set.
			*/
            $content['before']  = $cafe;
            $content['after']   = $request->all();

            /*
				Create a new cafe action and save the action for an
				admin to approve.
			*/
            $cafeAction                 = new CafeAction();

            $cafeAction->cafe_id        = $cafe->id;
            $cafeAction->user_id        = Auth::user()->id;
            $cafeAction->status         = 1;
            $cafeAction->type           = 'cafe-updated';
            $cafeAction->content        = json_encode($content);
            $cafeAction->processed_by   = Auth::user()->id;
            $cafeAction->processed_on   = date('Y-m-d H:i:s', time());
            $cafeAction->save();


            /*
				If the company ID is not empty, load the company being
				edited.
			*/
            if ($companyID != '') {
                /*
                    Company we are updating the content for
                */
                $company = Company::where('id', '=', $companyID)->first();
                /*
                    If the request has a company name, update the company name.
                */
                if ($request->has('company_name')) {
                    $company->name = $request->get('company_name');
                }
                /*
                    If the request has a company type, update the company type.
                */
                if ($request->has('company_type')) {
                    $company->roaster = $request->get('company_type') == 'roaster' ? 1 : 0;
                }
                /*
                    If the request has a website, update the website.
                */
                if ($request->has('website')) {
                    $company->website = $request->get('website');
                }
                $company->logo          = '';
                $company->description   = '';
                /*
                    Save the company
                */
                $company->save();
            } else {
                /*
                    Create a new company
                */
                $company = new Company();
                /*
                    If the request has a company name, update the company name.
                */
                if ($request->has('company_name')) {
                    $company->name = $request->get('company_name');
                }
                /*
                    If the request has a company type, add the type but default to not a roaster.
                */
                if ($request->has('company_type')) {
                    $company->roaster = $request->get('company_type') == 'roaster' ? 1 : 0;
                } else {
                    $company->roaster = 0;
                }
                /*
                    If the request has a website, add the company website.
                */
                if ($request->has('website')) {
                    $company->website = $request->get('website');
                }

                $company->logo          = '';
                $company->description   = '';
                $company->added_by      = Auth::user()->id;
                /*
                    Save the company.
                */
                $company->save();
            }


            /*
				Grab the cafe we are updating.
			*/
            $cafe = Cafe::where('slug', '=', $slug)->first();

            /*
				If the request has an address, update the address or
				using the existing address
			*/
            if ($request->has('address')) {
                $address = $request->get('address');
            } else {
                $address = $cafe->address;
            }

            /*
				If the request has an city, update the city or
				using the existing city
			*/
            if ($request->has('city')) {
                $city = $request->get('city');
            } else {
                $city = $cafe->city;
            }
            /*
				If the request has an city, update the city or
				using the existing city
			*/
            if ($request->has('state')) {
                $state = $request->get('state');
            } else {
                $state = $cafe->state;
            }

            /*
				If the request has an zip, update the zip or
				using the existing zip
			*/
            if ($request->has('zip')) {
                $zip = $request->get('zip');
            } else {
                $zip = $cafe->zip;
            }

            /*
				If the request has an location name, update the location name or
				using the existing location name
			*/
            if ($request->has('location_name')) {
                $locationName = $request->get('location_name');
            } else {
                $locationName = $cafe->location_name;
            }

            /*
                            If the request has brew methods, decode and set to the brew methods
                            variable.
                        */
            if ($request->has('brew_methods')) {
                $brewMethods = json_decode($request->get('brew_methods'));
            }

            /*
                Grab the lat and lng from the request
            */
            $lat = $request->get('lat') != '' ? $request->get('lat') : 0;
            $lng = $request->get('lng') != '' ? $request->get('lng') : 0;
            /*
                    If needed, update the latitude and longitude if not set.
                */
            if ($lat == 0 && $lng == 0) {
                //Google's free trial is limited and may return null which makes geocoding to fail
//                $coordinates = GoogleMaps::geocodeAddress($address, $city, $state, $zip);
//                $lat = $coordinates['lat'];
//                $lng = $coordinates['lng'];

                $lat = -40;
                $lng = 70;
            }
            /*
                    Update all of the cafe data to the new data.
                */
            $cafe->company_id       = $company->id;
            $cafe->location_name    = $locationName != null ? $locationName : '';
            $cafe->address          = $address;
            $cafe->city             = $city;
            $cafe->state            = $state;
            $cafe->zip              = $zip;
            $cafe->latitude         = $lat;
            $cafe->longitude        = $lng;
            $cafe->added_by         = Auth::user()->id;
            /*
                    If the request has matcha, apply the matcha flag.
                */
            if ($request->has('matcha')) {
                $cafe->matcha = $request->get('matcha');
            }
            /*
                    If the request has tea, apply the tea flag.
                */
            if ($request->has('tea')) {
                $cafe->tea = $request->get('tea');
            }
            /*
                    Save the cafe
                */
            $cafe->save();

            /*
                     If the request has brew methods, sync the brew methods to what has
                     been updated
                 */
            if ($request->has('brew_methods')) {
                /*
                Attach the brew methods
              */
                $cafe->brewMethods()->sync($brewMethods);
            }
            /*
                    Load the company and return it.
                */
            $company = Company::where('id', '=', $company->id)
                        ->with('cafes')
                        ->first();
            /*
		    Return the edited cafes as JSON
		  */
            return response()->json($company, 200);
        } else {
            /*
                Grab the cafe being updated
            */
            $cafe = Cafe::where('slug', '=', $slug)->with('company')->first();

            $cafe = Cafe::where('slug', '=', $slug)->first();

            /*
                    Set the before cafe to the data that was existing,
                    and the after to what was set.
                */
            $content['before'] = $cafe;
            $content['after'] = $request->all();


            /*
                    Create a new cafe action and save the action for an
                    admin to approve.
                */
            $cafeAction             = new CafeAction();
            $cafeAction->cafe_id    = $cafe->id;
            $cafeAction->user_id    = Auth::user()->id;
            $cafeAction->status     = 0;
            $cafeAction->type       = 'cafe-updated';
            $cafeAction->content    = json_encode($content);
            $cafeAction->save();

            if ($request->has('brew_methods')) {
                /*
                    Return a flag for cafe updates pending
                */
                return response()->json(['cafe_updates_pending' => $request->get('company_name')]);
            }
        }
    }

    public function postNewCafe(StoreCafeRequest $request)
    {
        $companyID = $request->get('company_id');

        /*
			Get the company. If its null, create a new company otherwise
			set to the company that exists.
		*/
        $company = Company::where('id', '=', $companyID)->first();
        $company = $company == null ? new Company() : $company;

        /*
            Determines if the user can create a cafe or not.
            If the user can create a cafe, then we let them otherwise
            we create an add cafe action.
        */
        if (Auth::user()->can('create', [Cafe::class, $company])) {
            /*
                Grabs the company ID.
            */
            $companyID = $request->get('company_id');

            /*
				If the company exists, load the company. If the company
				does not exist, create a new company with what was
				sent from the user.
			*/
            if ($companyID != '') {
                $company = Company::where('id', '=', $companyID)->first();
            } else {
                $company = new Company();

                $company->name          = $request->get('company_name');
                $company->roaster       = $request->get('company_type') == 'roaster' ? 1 : 0;
                $company->website       = $request->get('website');
                $company->logo          = '';
                $company->description   = '';
                $company->added_by      = Auth::user()->id;
                $company->save();
            }

            /*
                Grab all of the new cafe data
             */
            $address        = $request->get('address');
            $city           = $request->get('city');
            $state          = $request->get('state');
            $zip            = $request->get('zip');
            $locationName   = $request->get('location_name');
            $brewMethods    = json_decode($request->get('brew_methods'));

            $lat = $request->get('lat') != '' ? $request->get('lat') : 0;
            $lng = $request->get('lng') != '' ? $request->get('lng') : 0;

            if ($lat == 0 && $lng == 0) {
                //Google's free trial is limited and may return null which makes geocoding to fail
//                $coordinates = GoogleMaps::geocodeAddress($address, $city, $state, $zip);
//                $lat = $coordinates['lat'];
//                $lng = $coordinates['lng'];

                $lat = -25;
                $lng = 75;
            }

            /*
                    Create a new cafe
                */
            $cafe = new Cafe();

            $cafe->company_id       = $company->id;
            $cafe->slug             = SlugService::createSlug(Cafe::class, 'slug', $company->name . ' ' . $locationName . ' ' . $address . ' ' . $city . ' ' . $state);
            $cafe->location_name    = $locationName != null ? $locationName : '';
            $cafe->address          = $address;
            $cafe->city             = $city;
            $cafe->state            = $state;
            $cafe->zip              = $zip;
            $cafe->latitude         = $lat;
            $cafe->longitude        = $lng;
            $cafe->added_by         = Auth::user()->id;
            $cafe->tea              = $request->has('tea') ? $request->get('tea') : 0;
            $cafe->matcha           = $request->has('matcha') ? $request->get('matcha') : 0;
            $cafe->deleted          = 0;
            $cafe->save();

            /*
                Attach the brew methods
            */
            $cafe->brewMethods()->sync($brewMethods);

            /*
                Create an already processed and approved action for the
                user since they have permission.
            */
            $cafeAction                 = new CafeAction();
            $cafeAction->user_id        = Auth::user()->id;
            $cafeAction->status         = 1;
            $cafeAction->type           = 'cafe-added';
            $cafeAction->content        = json_encode($request->all());
            $cafeAction->processed_by   = Auth::user()->id;
            $cafeAction->processed_on   = date('Y-m-d H:i:s', time());
            $cafeAction->save();
            /*
                Grab the company to return
            */
            $company =  Company::where('id', '=', $company->id)
                        ->with('cafes')
                        ->first();
            /*
                Return the added cafes as JSON
            */
            return response()->json($company, 201);

        } else {
            /*
                Create a new cafe action and save all of the data
                that the user has provided
            */
            $cafeAction             = new CafeAction();
            $cafeAction->user_id    = Auth::user()->id;
            $cafeAction->status     = 0;
            $cafeAction->type       = 'cafe-added';
            $cafeAction->content    = json_encode($request->all());
            $cafeAction->save();

            /*
                Return the flag that the cafe addition is pending
            */
            return response()->json(['cafe_add_pending' => $request->get('company_name')]);
        }
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
        /*
			Grabs the Cafe to be deleted
		*/
        $cafe = Cafe::where('slug', '=', $slug)->first();
        /*
                    Checks if the user can delete the cafe through
                    our CafePolicy.
                */
        if( Auth::user()->can('delete', $cafe ) ){
            $cafe->deleted = 1;
            $cafe->save();
            /*
                Creates an action that tracks and approves a cafe deletion.
            */
            $cafeAction 					= new CafeAction();
            $cafeAction->cafe_id 			= $cafe->id;
            $cafeAction->user_id			= Auth::user()->id;
            $cafeAction->status 			= 1;
            $cafeAction->type 				= 'cafe-deleted';
            $cafeAction->content 			= '';
            $cafeAction->processed_by	    = Auth::user()->id;
            $cafeAction->processed_on       = date('Y-m-d H:i:s', time() );
            $cafeAction->save();

            return response()->json('', 204);

        }else {
            /*
                Get the cafe to create the action.
            */
            $cafe = Cafe::where('slug', '=', $slug)->with('company')->first();
            /*
                Creates an action that tracks and approves a cafe deletion.
            */
            $cafeAction             = new CafeAction();
            $cafeAction->cafe_id    = $cafe->id;
            $cafeAction->user_id    = Auth::user()->id;
            $cafeAction->status     = 0;
            $cafeAction->type       = 'cafe-deleted';
            $cafeAction->content    = '';
            $cafeAction->save();
            /*
                Return the cafe delete pending
            */
            return response()->json(['cafe_delete_pending' => $cafe->company->name]);
        }
    }
}
