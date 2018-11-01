<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use App\Http\Resources\SettingCollection;
use App\Http\Resources\Setting as SettingResource;
use App\Http\Resources\PostCollection;
use App\Http\Resources\Post as PostResource;
use App\Post;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return setting()->all();
    }


    public function change(Request $request)
    {
        $key = $request->key;
        $value = $request->value;
        setting([$key => $value])->save();
        return setting()->all();
    }
    public function test(Request $request){
        $cats = Post::find(1);
        //return response()->json($cats->categories);
        /*foreach ($cats->categories as $cat) {
            return response()->json($cat);
        }*/
        //return json_encode($a);
        //return json_encode(Post::find(3)->categorys()->get());
        //return new PostResource(Post::find(1));
       return new PostCollection(Post::all());
        //return $request->test;
    }
}
