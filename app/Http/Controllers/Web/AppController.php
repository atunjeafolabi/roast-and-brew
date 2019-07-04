<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function getApp(Request $request){

        if( $request->has('ref') ){
            return redirect('/');
        }

        return view('app');
    }

//    public function getLogin(){
//        return view('login');
//    }

    public function getLogout(){

        Auth::logout();

        return redirect('/');
    }
}