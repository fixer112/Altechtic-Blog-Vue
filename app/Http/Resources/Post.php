<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class Post extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $data = [
            'categorys' => $this->categories, 
            'user'=> $this->user,
            'comment'=> $this->comment,
        ];
        return parent::toArray($request);
    }

     /*public function with($request){
        return [
            //'can_edit' => Auth::user()->hasPermissionTo('edit articles'),
            //'can_publish' => Auth::user()->hasPermissionTo('publish articles'),
            //'post_user' => $this->user->id == Auth::user()->id,
        ];
    }*/
}
