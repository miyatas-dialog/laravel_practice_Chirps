<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Follow extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性
     */
    protected $fillable = [
        'follower_id',
        'following_id',
    ];

    /**
     * フォローしているユーザーのリレーション
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * フォローされているユーザーのリレーション
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
