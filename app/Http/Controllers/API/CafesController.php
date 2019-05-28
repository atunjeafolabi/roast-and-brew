<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Cafe;

class CafesController extends Controller
{
    public function getCafes()
    {
        $cafes = Cafe::all();

        return response()->json( $cafes );  //use api resources instead
    }

    public function getCafe( $id )
    {
        $cafe = Cafe::where('id', '=', $id)->first();

        return response()->json( $cafe );
    }

    public function postNewCafe(Request $request)
    {
        $cafe = new Cafe();

        $cafe->name         = $request->name;
        $cafe->address      = $request->address;
        $cafe->city         = $request->city;
        $cafe->state        = $request->state;
        $cafe->zip          = $request->zip;
        $cafe->latitude     = '050';
        $cafe->longitude    = '020';

        $cafe->save();

        return response()->json($cafe, 201);
    }
}
