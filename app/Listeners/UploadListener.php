<?php

namespace App\Listeners;

use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Image;
class UploadListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ImageWasUploaded  $event
     * @return void
     */
    public function handle(ImageWasUploaded $event)
    {
         /*$method = 'on'.class_basename($event);
         if (method_exists($this, $method)) {
             call_user_func([$this, $method], $event);
         }*/
         
         $path = $event->path();
         //your code, for example resizing and cropping
         $img = Image::make($path);
         $width = ($img->width() > 640 ? 640 : null);
         $height = ($img->height() > 426 ? 426 : null);
         $img->resize($width, $height);
         $img->save($path);
         
         
         
     }

    /* public function onImageWasUploaded(ImageWasUploaded $event)
     {
         $path = $event->path();
         //your code, for example resizing and cropping
         $img = Image::make($path);
         $img->resize(500, 500);
         $img->save($path);
         Log::debug('image path '.$path);
     }*/
 }
