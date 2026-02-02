<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // C001~C005
        User::create([
            'name' => 'ユーザー1',
            'email' => 'user1@test.com',
            'email_verified_at' => '2025-10-16 22:52:10',
            'password' => Hash::make('password'),
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => '渋谷ビル1F',
            'img_url' => 'user_images/icon1.png',
        ]);

        // C006~C010
        User::create([
            'name' => 'ユーザー2',
            'email' => 'user2@test.com',
            'email_verified_at' => '2025-10-16 22:52:10',
            'password' => Hash::make('password'),
            'postcode' => '234-5678',
            'address' => '東京都新宿区',
            'building' => '新宿ビル2F',
            'img_url' => 'user_images/icon2.png',
        ]);

        // 商品紐づけなし
        User::create([
            'name' => 'ユーザー3',
            'email' => 'user3@test.com',
            'email_verified_at' => '2025-10-16 22:52:10',
            'password' => Hash::make('password'),
            'postcode' => '444-4444',
            'address' => 'テスト県',
            'building' => ' テストビル1F',
            'img_url' => 'user_images/icon3.png',
        ]);
    }
}