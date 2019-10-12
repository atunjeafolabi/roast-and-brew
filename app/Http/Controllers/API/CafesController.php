<?php

namespace App\Http\Controllers\API;

//use Request;
use Auth;
use DB;
use File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\StoreCafeRequest;
use App\Http\Requests\EditCafeRequest;
use App\Models\Cafe;
use App\Utilities\Tagger;
use App\Models\CafePhoto;
use App\Models\Company;
use App\Models\CafeAction;
use App\Services\CafeService;
use App\Services\CafeActionService;

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
            ->with(['tags' => function ($query) {
                $query->select('tag');
            }])
            ->with('company')
            ->withCount('userLike')
            ->where('deleted', '=', 0)
            ->get();

        return response()->json($cafes);  //can use Laravel api resources instead
    }

    public function getCafe($slug)
    {
        $cafe = Cafe::where('slug', '=', $slug)
            ->with('brewMethods')
            ->withCount('userLike')
            ->with('tags')
            ->with(['company' => function ($query) {
                $query->withCount('cafes');
            }])
            ->withCount('likes')
            ->where('deleted', '=', 0)
            ->first();

        if ($cafe != null) {
            return response()->json($cafe);
        } else {
            abort(404);
        }
    }

    public function getCafeEditData($slug)
    {
        /*
            Grab the cafe with the parent of the cafe
        */
        $cafe = Cafe::where('slug', '=', $slug)
            ->with('brewMethods')
            ->withCount('userLike')
            ->with(['company' => function ($query) {
                $query->withCount('cafes');
            }])
            ->where('deleted', '=', 0)
            ->first();
        /*
           Return the cafe queried.
       */
        return response()->json($cafe);
    }

    public function putEditCafe($slug, EditCafeRequest $request)
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
            $content['before'] = $cafe;
            $content['after'] = $request->all();

            /*
				Create a new cafe action and save the action for an
				admin to approve.
			*/
            CafeActionService::createApprovedAction($cafe->id, $cafe->company_id, 'cafe-updated', $content);

            $updatedCafe = CafeService::editCafe($cafe->id, $request->all());

            /*
                    Load the company and return it.
                */
            $company = Company::where('id', '=', $updatedCafe->company_id)
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
            CafeActionService::createPendingAction($cafe->id, $cafe->company_id, 'cafe-updated', $content);

            if ($request->has('brew_methods')) {
                /*
                    Return a flag for cafe updates pending
                */
                return response()->json(['cafe_updates_pending' => $request->get('company_name')], 202);
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

            $cafe = CafeService::addCafe($request->all(), Auth::user()->id);

            CafeActionService::createApprovedAction(null, $cafe->company_id, 'cafe-added', $request->all());

            /*
                Grab the company to return
            */
            $company = Company::where('id', '=', $cafe->company->id)
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
            CafeActionService::createPendingAction(null, $request->get('company_id'), 'cafe-added', $request->all());

            /*
                Return the flag that the cafe addition is pending
            */
            return response()->json(['cafe_add_pending' => $request->get('company_name')], 202);
        }
    }

    public function postLikeCafe($slug, Request $request)
    {

        $cafe = Cafe::where('slug', '=', $slug)->first();

        /*
            If the user doesn't already like the cafe, attaches the cafe to the user's likes
        */
        if (!$cafe->likes->contains(Auth::user()->id)) {

            $cafe->likes()->attach(Auth::user()->id, [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return response()->json(['cafe_liked' => true], 201);
    }

    public function deleteLikeCafe($slug)
    {

        $cafe = Cafe::where('slug', '=', $slug)->first();

        $cafe->likes()->detach(Auth::user()->id);

        return response(null, 204);
    }

    public function postAddTags(Request $request, $slug)
    {

        $tags = $request->get('tags');

        $cafe = Cafe::where('slug', '=', $slug)->first();

        /*
          Tags the cafe
        */
        Tagger::tagCafe($cafe, $tags);

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

    public function deleteCafeTag($slug, $tagID)
    {

        DB::statement(
            'DELETE FROM cafes_users_tagsWHERE cafe_id = "' . $slug . '" AND tag_id = "' . $tagID .
            '" AND user_id = "' . Auth::user()->id . '"'
        );

        /*
          Return a proper response code for successful untagging
        */
        return response(null, 204);
    }

    public function deleteCafe($slug)
    {
        /*
			Grabs the Cafe to be deleted
		*/
        $cafe = Cafe::where('slug', '=', $slug)->first();
        /*
            Checks if the user can delete the cafe through
            our CafePolicy.
        */
        if (Auth::user()->can('delete', $cafe)) {

            $cafe->deleted = 1;
            $cafe->save();
            /*
                Creates an action that tracks and approves a cafe deletion.
            */
            CafeActionService::createApprovedAction($cafe->id, $cafe->company_id, 'cafe-deleted', '');


            return response()->json('', 204);

        } else {
            /*
                Get the cafe to create the action.
            */
            $cafe = Cafe::where('slug', '=', $slug)->with('company')->first();
            /*
                Creates an action that tracks and approves a cafe deletion.
            */
            CafeActionService::createPendingAction($cafe->id, $cafe->company_id, 'cafe-deleted', '');

            /*
                Return the cafe delete pending
            */
            return response()->json(['cafe_delete_pending' => $cafe->company->name], 202);
        }
    }
}
