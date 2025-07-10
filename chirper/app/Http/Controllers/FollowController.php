<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * ユーザーをフォローする
     */
    public function follow(Request $request, User $user)
    {
        // 自分自身をフォローできないようにする
        if (Auth::id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => '自分自身をフォローすることはできません'
            ], 400);
        }

        try {
            Auth::user()->follow($user);
            
            return response()->json([
                'success' => true,
                'message' => 'フォローしました',
                'is_following' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'フォローに失敗しました'
            ], 500);
        }
    }
}
