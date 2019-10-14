<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    public function register(Request $req) {
        $json = $req->input('json');
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $role = 'USER_ROLE';

        if(!is_null($email) && !is_null($name) && !is_null($password)) {
            //encrypt pass
            $pwd = hash('sha256', $password);

            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;
            $user->password = $pwd;

            $isset_user = User::where('email', '=', $email)->first();

            if(is_null($isset_user) || (count($isset_user) == 0)) {

                $user->save();
                
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'User created'
                );

            } else {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'User already exist'
                );
            }

        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Error creating user'
            );
        }

        return response()->json($data);
        
    }

    public function login(Request $req) {
        $jwtAuth = new JwtAuth();

        $json = $req->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;

        //encrypt pass
        $pwd = hash('sha256', $password);

        if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')) {
            $signup = $jwtAuth->signup($email, $pwd);
            
        } elseif($getToken != null) {
//            var_dump($getToken); die();
            $signup = $jwtAuth->signup($email, $pwd, $getToken);

        } else {
            $signup = array(
                'status' => 'error',
                'message' => 'Check your params'
            );
        }
        return response()->json($signup, 200);
    }
}
