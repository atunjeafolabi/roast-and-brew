<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\BrewMethod;

class BrewMethodsController extends Controller
{
    public function getBrewMethods(){

        $brewMethods = BrewMethod::all();

        return response()->json($brewMethods, 201);

    }

    public function getBrewMethod(){

    }

    public function postNewBrewMethod(Request $request){

        $brewMethod = new BrewMethod();

        $brewMethod->save($request->all());

        return response()->json($brewMethod, 201);

    }
}
