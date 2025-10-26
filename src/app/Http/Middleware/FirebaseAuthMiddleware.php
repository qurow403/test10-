<?php

namespace App\Http\Middleware;

use Closure;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Http\Request;

class FirebaseAuthMiddleware
{
    protected $auth;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $verifiedIdToken = $this->auth->verifyIdToken($token);
            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $request->merge(['firebase_uid' => $firebaseUid]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        return $next($request);
    }

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }
}
