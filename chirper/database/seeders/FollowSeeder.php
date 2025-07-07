<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('follows')->insert([
            [
                'follower_id' => 1,  // user1がフォローする
                'following_id' => 2, // user2をフォロー
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'follower_id' => 2,  // user2がフォローする
                'following_id' => 1, // user1をフォロー
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
