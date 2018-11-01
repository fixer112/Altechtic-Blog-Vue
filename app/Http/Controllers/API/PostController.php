<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use carbon\Carbon;
use App\Post;
use App\Category;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;


class PostController extends Controller
{
    public function __construct(){

        $this->middleware('permission:create articles')->only('store');
        //$this->middleware('permission:delete articles')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rules = array(
            'type' => 'required|in:all,1,0,trash',
        );

        $validator = $this->validate($request, $rules);

        if ($request->type=='all') {
            $post = Post::where('trash','0')->get();
        }
        if ($request->type=='1') {
            $post = Post::where('trash','0')->where('status', '1')->get();
        }
        if ($request->type=='0') {
            $post = Post::where('trash','0')->where('status', '0')->get();
        }
        if ($request->type=='trash') {
            $post = Post::where('trash','1')->get();
        }
            $data= [
                'posts' => new PostCollection($post),
                'can_edit' => Auth::user()->hasPermissionTo('edit articles'),
                'can_publish' => Auth::user()->hasPermissionTo('publish articles'),
                'can_delete' => Auth::user()->hasPermissionTo('delete articles'),
                'auth_id' => Auth::user()->id,
            ];
            return response()->json($data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->create_permission()) {
              return response()->json($this->create_permission($post));
            }

        $data=[
            'publish' => Auth::user()->hasPermissionTo('publish articles'),
            'edit' => Auth::user()->hasPermissionTo('edit articles'),
            'categorys' => Category::all(),
        ];
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->create_permission()) {
              return response()->json($this->create_permission($post));
            }

        $rules = array(
            'title' => 'required|string|min:10|max:50',
            'slug' => 'required|alpha_dash|min:10|max:100',
            'body' => 'required|string|min:50|max:5000',
            'photo' => 'image|mimes:jpeg,jpg,png|max:1024',
            'status' => 'required|in:1,0',
        );

        $validator = $this->validate($request, $rules);
        $title = preg_replace('/\s+/', ' ', $request->title);
        $title = preg_replace('/-+/', ' ', $title);
        $title = ucfirst(strip_tags($title));
        $slug = preg_replace('/\s+/', '-', $request->slug);
        $slug = preg_replace('/-+/', '-', $slug);

        /*if ($request->status!='draft' || $request->status!='publish') {
            $data = ['fail' => 'status can only be draft or publish'];
            return  response()->json($data);
        }*/
        $post = Post::create([
            'title' => trim($title, '-'),
            'slug' => trim($slug, '-'),
            'body' => $request->body,
            'status' => $request->status,
            'user_id' => Auth::user()->id,
            //'category_id' => 1,
        ]);

        if (!Auth::user()->hasPermissionTo('publish articles')) {
            $post->update(['status' => '0']);
        }
        if ($request->hasFile('photo')) {
        $now = Carbon::now()->format('m-Y');
        $identity = $request->file('photo');
        $identityname = time().".".$identity->getClientOriginalExtension();
        Image::make($request->file('photo'))->resize(500, 500)->save(public_path('images/'.$now.'/'.$identityname));
        $post->update(['image' => '/images/'.$now.'/'.$identityname]);

        
        }
        if ($request->photo_url) {
            //$url = preg_replace('/,.*/', '', $request->photo_url);
            $post->update(['image' => $request->photo_url]);
        }
        if ($request->category) {
            $category = json_decode($request->category);
            $category = Category::find($category);
            $post->categories()->sync($category);
        }

        return new PostResource($post);
        /*if ($request->trash) {
         $post->update(['trash' => $request->trash]);
        }*/
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
        $post = Post::findOrFail($id);
        
        if ($this->edit_publish_permission($post)) {
          return response()->json($this->edit_publish_permission($post));
        }

        $data=[
            'post' => new PostResource($post),
            'can_edit' => Auth::user()->hasPermissionTo('edit articles'),
            'can_publish' => Auth::user()->hasPermissionTo('publish articles'),
            'can_delete' => Auth::user()->hasPermissionTo('delete articles'),
            'auth_id' => Auth::user()->id == $post->user_id,
            'categorys' => Category::all(),
        ];
        return response()->json($data);
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
        $post = Post::findOrFail($id);

        if ($this->edit_publish_permission($post)) {
          return response()->json($this->edit_publish_permission($post));
        }

        $rules = array(
            'title' => 'string|min:10|max:50',
            'slug' => 'alpha_dash|min:10|max:100',
            'body' => 'string|min:50|max:5000',
            'photo' => 'image|mimes:jpeg,jpg,png|max:1024',
            'status' => 'required|in:1,0,trash',
        );

        $validator = $this->validate($request, $rules);

        /*if (!Auth::user()->hasPermissionTo('unpublish articles')  && $post->user_id != Auth::user()->id){
            $data=['fail' => 'You dont have permission'];
            return response()->json($data);
        }else {*/
            if ($request->status!='trash') {
                
            if ($this->publish_permission($post)) {
              return response()->json($this->publish_permission($post));
            }
                $post->update(['status' => $request->status]);
                $post->update(['trash' => '0']);
            }

            if ($request->status=='trash') {

                if ($this->publish_permission($post)) {
              return response()->json($this->publish_permission($post));
            }

                $post->update(['trash' => '1']);
            }
        //}
             if (!Auth::user()->hasPermissionTo('publish articles')) {
            $post->update(['status' => '0']);
        }

        

   /* if (!Auth::user()->hasPermissionTo('edit articles')  && $post->user_id != Auth::user()->id){
            $data=['fail' => 'You dont have permission'];
            return response()->json($data);
        }else{*/
        if ($request->title) {
            if ($this->edit_permission($post)) {
              return response()->json($this->edit_permission($post));
            }

            $title = preg_replace('/\s+/', ' ', $request->title);
            $title = preg_replace('/-+/', ' ', $title);
            $title = ucfirst(strip_tags($title));
            $post->update(['title' => trim($title, '-')]);
        }

        if ($request->slug) {

            if ($this->edit_permission($post)) {
              return response()->json($this->edit_permission($post));
            }

            $slug = preg_replace('/\s+/', '-', $request->slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $post->update(['slug' => trim($slug, '-')]);
        }

        if ($request->body) {

            if ($this->edit_permission($post)) {
              return response()->json($this->edit_permission($post));
            }

            $post->update(['body' => $request->body]);
        }

        if ($request->photo_url) {

            if ($this->edit_permission($post)) {
              return response()->json($this->edit_permission($post));
            }

            $post->update(['image' => $request->photo_url]);
        }

        if ($request->hasFile('photo')) {

            if ($this->edit_permission($post)) {
              return response()->json($this->edit_permission($post));
            }

        $now = Carbon::now()->format('m-Y');
        $identity = $request->file('photo');
        $identityname = time().".".$identity->getClientOriginalExtension();
        Image::make($request->file('photo'))->resize(500, 500)->save(public_path('images/'.$now.'/'.$identityname));
        $post->update(['image' => '/images/'.$now.'/'.$identityname]);

        
        }

        if ($request->category) {

            if ($this->edit_permission($post)) {
              return response()->json($this->edit_permission($post));
            }

            $category = json_decode($request->category);
            $category = Category::find($category);
            $post->categories()->sync($category);
            //$post->update(['category_id' => $request->category]);
        }

        $data= [
                'posts' => new PostCollection(Post::all()),
                'post' => new PostResource($post),
                'can_edit' => Auth::user()->hasPermissionTo('edit articles'),
                'can_publish' => Auth::user()->hasPermissionTo('publish articles'),
                'can_delete' => Auth::user()->hasPermissionTo('delete articles'),
                'auth_id' => Auth::user()->id == $post->user_id,
                'categorys' => Category::all(),
            ];
            return response()->json($data);
        
    //}


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if (!Auth::user()->hasPermissionTo('delete articles') && $post->user_id != Auth::user()->id){
            $data['errors'] = ['fail' =>['You dont have permission to delete this post']];
            //$data=['fail' => 'You dont have permission to delete this post'];
            return response()->json($data);
       }
        $post->delete();
        $data= [
                'posts' => new PostCollection(Post::all()),
            ];
            return response()->json($data);
    }


    public function edit_publish_permission($post){
        if (!Auth::user()->hasAnyPermission('edit articles','publish articles')  && $post->user_id != Auth::user()->id){
            $data['errors'] = ['fail' =>['You dont have permission to update this post']];
            //$data=['fail' => 'You dont have permission to update this post'];
            return response()->json($data);
       }
    }
    public function edit_permission($post){
        if (!Auth::user()->hasPermissionTo('edit articles')  && $post->user_id != Auth::user()->id){
            $data['errors'] = ['fail' =>['You dont have permission to edit']];
            //$data=['fail' => 'You dont have permission to edit'];
            return response()->json($data);
        }
    }
    public function publish_permission($post){
        if (!Auth::user()->hasPermissionTo('publish articles')  && $post->user_id != Auth::user()->id){
            $data['errors'] = ['fail' =>['You dont have permission to publish']];
            //$data=['fail' => 'You dont have permission to publish'];
            return response()->json($data);
        }
    }
    public function create_permission(){
        if (!Auth::user()->hasPermissionTo('create articles')){
            $data['errors'] = ['fail' =>['You dont have permission to create']];
            //$data=['fail' => 'You dont have permission to create'];
            return response()->json($data);
        }
    }
}
