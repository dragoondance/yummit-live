<?php

namespace App\Http\Middleware;

use Closure;

class RestaurantProvider
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
    config(['auth.guards.restaurant-api.provider' => 'restaurants']);
    return $next($request);
  }
}
