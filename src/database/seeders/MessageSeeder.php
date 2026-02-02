<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    public function run()
    {
        Message::create([
            'transaction_id' => 1,
            'user_id'        => 2,
            'message'        => '購入希望です。まだ在庫ありますか？',
        ]);

        Message::create([
            'transaction_id' => 1,
            'user_id'        => 1,
            'message'        => 'はい、あります！そのまま購入で大丈夫ですよ。',
        ]);

        Message::create([
            'transaction_id' => 3,
            'user_id'        => 1,
            'message'        => '商品の状態をもう少し詳しく教えてください。',
        ]);

        Message::create([
            'transaction_id' => 3,
            'user_id'        => 2,
            'message'        => '目立った傷はなく、動作も問題ありません。',
        ]);
    }
}
