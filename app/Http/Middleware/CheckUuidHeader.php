<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class CheckUuidHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
   public function handle(Request $request, Closure $next): Response
    {
        $expectedUuid = config('services.api_libre.uuid');

        if ($request->header('UUID') != $expectedUuid) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        return $next($request);
    }
}
