<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Image;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserCollection;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
      $this->middleware('role:super-admin|moderator')->only('index');
    }

    public function index()
    {   
      if ($this->has_super_moderate()) {
        return response()->json($this->has_super_moderate());
      }

      $data=[];
      $data['users'] =  new UserCollection(User::all());
      $data['publish'] = Auth::user()->hasPermissionTo('publish articles');
      return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $data =[];
      $user = User::findOrFail($id);

      if ($this->has_super_moderate_id($id)) {
        return response()->json($this->has_super_moderate_id($id));
      }

      if ($user->hasRole('super-admin') && Auth::user()->id != $id) {
        $data['errors'] = ['fail' =>['You can not edit a super-admin']];
        //$data['fail'] ="You can not edit a super-admin";
        return response()->json($data);
      }

      $data = [
        'user' => $user,
        'userroles'=> $user->getRoleNames(),
        'roles' => Role::all(),
        'admin_mod' => Auth::user()->hasAnyRole('super-admin|moderator'),
        'admin_mod_id' => Auth::user()->hasAnyRole('super-admin|moderator')  || $id == Auth::user()->id,
        'auth_id' => Auth::user()->id == $user->id,
      ];
      return response()->json($data);
        //return new UserResource($User);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $user = User::findOrFail($id);

      if ($this->has_super_moderate_id($id)) {
        return response()->json($this->has_super_moderate_id($id));
      }

      $rules = array(
        'firstname' => 'alpha|min:3|max:15',
        'lastname' => 'alpha|min:3|max:15',
        'email' => 'string|email|max:30|unique:users',
        'password' => 'string|min:5|confirmed',
        'photo' => 'image|mimes:jpeg,jpg,png|max:300',
        'facebook' => 'nullable|string|max:20',
        'twitter' => 'nullable|string|max:20',
      );

      $validator = $this->validate($request, $rules);
      
      $identityname ="";
      $data = [];

      if ($user->hasRole('super-admin') && Auth::user()->id != $id) {
         $data['errors'] = ['fail' =>['You can not edit a super-admin']];
        //$data['fail'] ="You can not edit a super-admin";
        return response()->json($data);
      }

      if ($request->hasFile('photo')) {
        //$destination = public_path('/images');
        $identity = $request->file('photo');
        $identityname = $user->email.".".$identity->getClientOriginalExtension();
        //$identity->move($destination, $identityname);
        Image::make($request->file('photo'))->resize(150, 150)->save(public_path('images/avatar/'.$identityname));
        $user->update(['photo' => '/images/avatar/'.$identityname]);

        $png = '/images/avatar/'.$user->email.".png";
        $jpeg = '/images/avatar/'.$user->email.".jpeg";
        $jpg = '/images/avatar/'.$user->email.".jpg";
        $photos = [$png, $jpeg, $jpg];
        $pic = $user->photo;
        //if (($key = array_search($pic, $photos)) !=false) {
        $key = array_search($pic, $photos);
        unset($photos[$key]);
        //}

        foreach ($photos as $photo) {
          $path = public_path().$photo;
          
          if (file_exists($path)) {
            unlink($path);
          }
          
        } 
      }

      if ($request->password) {
        if (Auth::user()->id != $user->id) {
          $data['errors'] = ['fail' =>['You dont have permission to change user password']];
            return response()->json($data);
        }
            //$old = Hash::make($request->oldpass);
        if (Hash::check($request->oldpass, $user->password)) {
          $user->update(['password' => Hash::make($request->password)]);
        }else {
          $data['errors'] = ['fail' =>['Old password is invalid']];
          //$data['fail']= "Old password is invalid";
          return response()->json($data);
        }

      }

      if ($request->role) {

        if (!$user->hasRole('super-admin')) {
          if ($request->role!='super-admin') {
            $user->syncRoles([$request->role]);
          }else {
            $data['errors'] = ['fail' =>['User cant be assigned super-admin']];
            //$data['fail']= "User cant be assigned super-admin";
            return response()->json($data);
          }
          
        } else {
          $data['errors'] = ['fail' =>['super-admin role cant be changed']];
          //$data['fail']= "super-admin role cant be changed";
          return response()->json($data);
        }
      }
      
      if ($request->firstname) {
        $user->update(['firstname' => $request->firstname]);
      }
      if ($request->lastname) {
        $user->update(['lastname' => $request->lastname]);
      }
      if ($request->email) {
        $user->update(['email' => $request->email]);
      }
      if ($request->facebook) {
        $user->update(['facebook' => $request->facebook]);
      }
      if ($request->twitter) {
        $user->update(['twitter' => $request->twitter]);
      }

      $data['success'] = 'User updated successfully';
      $data['user'] = $user;
        //$data['name'] = $request->all();
      return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $user = User::findOrFail($id);

      if ($this->has_super_moderate_id($id)) {
        return response()->json($this->has_super_moderate_id($id));
      }
      
      $data=[];
      if ($user->hasRole('super-admin')) {
        $data['errors'] = ['fail' =>['You dont have permission to delete a super-admin']];
        //$data['fail'] = "You dont have permission to delete a super-admin";
        return response()->json($data);
      }

      $user->delete();

      return new UserCollection(User::all());
    }

    public function has_super_moderate_id($id){
      if (!Auth::user()->hasAnyRole('super-admin|moderator')  && $id != Auth::user()->id){
        $data['errors'] = ['fail' =>['You dont have permission to edit user']];
        //$data = ['fail' => 'You dont have permission to edit user'];
        return $data;
      }
    }

    public function has_super_moderate(){
      if (!Auth::user()->hasAnyRole('super-admin|moderator')){
        $data['errors'] = ['fail' =>['You dont have permission for this action']];
        //$data=['fail' => 'You dont have permission for the action'];
        return response()->json($data);
      }
    }
  }
