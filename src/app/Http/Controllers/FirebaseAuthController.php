<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use App\Models\User;

class FirebaseAuthController extends Controller
{
    protected $auth;

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        try {
            $verifiedIdToken = $this->auth->verifyIdToken($request->token);
            $uid = $verifiedIdToken->claims()->get('sub');

            $firebaseUser = $this->auth->getUser($uid);

            $user = User::firstOrCreate(
                ['firebase_uid' => $uid],
                ['name' => $firebaseUser->displayName ?? 'ゲストユーザー']
            );

            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'トークン検証に失敗しました: ' . $e->getMessage(),
            ], 401);
        }
    }
}
