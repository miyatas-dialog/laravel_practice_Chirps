<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function chirps(): HasMany
    {
        return $this->hasMany(Chirp::class);
    }

    /**
     * このユーザーがフォローしているユーザー
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
                    ->withTimestamps();
    }
    /**
     * このユーザーをフォローしているユーザー
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
                    ->withTimestamps();
    }
    /**
     * 指定したユーザーをフォローしているかチェック
     */
    public function isFollowing(User $user)
    {
        return $this->followings()->where('following_id', $user->id)->exists();
    }
    /**
     * 指定したユーザーをフォロー
     */
    public function follow(User $user)
    {
        if (!$this->isFollowing($user)) {
            $this->followings()->attach($user->id);
        }
    }
}
