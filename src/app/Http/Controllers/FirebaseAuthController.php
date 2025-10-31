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
        try {
        $idToken = $request->bearerToken();

        if (!$idToken) {
            \Log::error('🔥 Bearerトークンが存在しません');
            return response()->json(['success' => false, 'message' => 'Bearerトークンが存在しません'], 400);
        }

        \Log::info('✅ Bearerトークン受信: ' . substr($idToken, 0, 20) . '...');

        $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        $uid = $verifiedIdToken->claims()->get('sub');

        $firebaseUser = $this->auth->getUser($uid);

        $user = User::firstOrCreate(
            ['firebase_uid' => $uid],
            ['name' => $firebaseUser->displayName ?? 'ゲストユーザー']
        );

        return response()->json(['success' => true, 'user' => $user]);
    } catch (\Throwable $e) {
        \Log::error('❌ トークン検証エラー: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
    }
    }
}
