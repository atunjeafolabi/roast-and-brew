<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Request;
use Auth;

use App\Models\Company;


class CompaniesController extends Controller{

    public function getCompanySearch(){

        $term = Request::get('search');

        $companies = Company::where('name', 'LIKE', '%'.$term.'%')
                    ->withCount('cafes')
                    ->where('deleted', '=', 0)
                    ->get();

        return response()->json( ['companies' => $companies ] );
    }
}
