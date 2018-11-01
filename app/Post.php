<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
   protected $guarded = [];

  public function comment(){
  	return $this->hasMany('App\Comment');
  }

  public function categories(){
  	return $this->belongsToMany('App\Category')->withTimestamps();
  }

  public function user(){
  	return $this->belongsTo('App\User');
  }
}
