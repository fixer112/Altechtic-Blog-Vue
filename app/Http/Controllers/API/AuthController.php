<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\User as UserResource;

class AuthController extends Controller
{
	public function __construct()
    {
        //$this->middleware('auth:api');
    	//$this->middleware('guest');
    }
    
    public function register(Request $request){

    	$rules = array(
    		'firstname' => 'required|alpha|min:3|max:15',
    		'lastname' => 'required|alpha|min:3|max:15',
    		'username' => 'required|alpha_dash|min:3|max:10|unique:users',
            'email' => 'required|string|email|max:30|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:150',
            'facebook' => 'nullable|string|max:20',
            'twitter' => 'nullable|string|max:20',
		);

		$validator = $this->validate($request, $rules);
        $user = User::create([
            'firstname'=> $request->firstname,
            'lastname'=> $request->lastname,
            'username'=> $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if ($request->facebook) {
            $user->update(['facebook' => $request->facebook]);
        }
        if ($request->twitter) {
            $user->update(['twitter' => $request->twitter]);
        }

        $identityname ="avatar.jpg";

        if ($request->hasFile('photo')) {
        $destination = public_path('/images');
        $identity = $request->file('photo');
        $identityname = $request->email.".".$identity->getClientOriginalExtension();
        $identity->move($destination, $identityname);
        $user->update(['photo' => '/images'.'/'.$identityname]);
            
        }
		$data =[];

    	$user->assignRole('reader');

		$data['user'] = $user;
      	return response()->json($data, 201);

        
    }

    public function login(Request $request){
    	$rules = array(
    		'username' => 'required|string|alpha_dash|min:3|max:10|exists:users',
            'password' => 'required|string|min:5',
		);

		$validator = $this->validate($request, $rules);
		$data =[];

		$userdata = array(
        'username'     => $request->username,
        'password'  => $request->password,
    );
		
    	if ((!$token = JWTAuth::attempt($userdata)) || (!$web = Auth::guard('web')->attempt($userdata))) {
            $data['errors'] = ['fail' =>['Incorrect Password']];
    		//$data['fail'] = "Incorrect Password";
    		return response()->json($data, 422);
    	
      }

      	$request->session()->put('token', $token);
      	return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
      return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
        'auth' => Auth::guard('api')->check(),
        'auth_web' => Auth::guard('web')->check(),
      ]);

	}

}

