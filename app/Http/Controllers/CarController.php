<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\JwtAuth;

use App\Car;
use Validator;

use App\Http\Requests;

class CarController extends Controller
{
    public function index(Request $req) {
        $cars = Car::all()->load('user');

        return response()->json(array(
            'status' => 'success',
            'cars' => $cars
        ), 200);
        
    }

    public function show($id) {
        $car = Car::find($id)->load('user');

        return response()->json(array(
            'status' => 'success',
            'cars' => $car
        ), 200);
        
    }

    public function store(Request $req) {
        $hash = $req->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken) {
            $json = $req->input('json');
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            //user authenticated
            $user = $jwtAuth->checkToken($hash, true);

            $valid = Validator::make($params_array, [
 
                'title'=>'required|min:5',
                'description'=>'required',
                'price'=>'required',
                'status'=>'required',
            ]
            );
     
            if($valid -> fails()){
                return response()->json($valid->errors(), 400);
            }

            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->status = $params->status;
            $car->price = $params->price;

            $car->save();

            $data = array(
                'car' => $car,
                'status' => 'success',
                'code' => 200
            );
        } else {
            $data = array(
                'status' => 'error',
                'code' => 300,
                'message' => 'login failed'
            );
        }

        return response()->json($data, 200);
    }
}
