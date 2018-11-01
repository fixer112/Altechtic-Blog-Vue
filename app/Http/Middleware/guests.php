<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;

class guests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::guard('web')->check()){

            if ($request->expectsJson()) {
                $data['errors'] = ['firstname' =>['Please log out first.']];
                return response()->json($data, 401);
            }
            return redirect('/');
        }
        return $next($request);
    }
}
