<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\JwtAuth;


use App\Http\Requests;

class CarController extends Controller
{
    public function index(Request $req) {

        $hash = $req->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken) {
            echo "Authenticated";
        } else {
            echo "Not authenticated";
        }
        
    }
}
