<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\Transaction;

class RatingSeeder extends Seeder
{
    public function run()
    {
        // id が 2 と 4 のトランザクションだけ取得
        $transactions = Transaction::whereIn('id', [2, 4])->get();

        foreach ($transactions as $transaction) {
            $buyerId  = $transaction->buyer_id;
            $sellerId = $transaction->seller_id;

            if ($buyerId && $sellerId) {
                // 購入者 → 出品者
                Rating::create([
                    'transaction_id' => $transaction->id,
                    'from_user_id'   => $buyerId,
                    'to_user_id'     => $sellerId,
                    'score'          => rand(1, 5),
                ]);

                // 出品者 → 購入者
                Rating::create([
                    'transaction_id' => $transaction->id,
                    'from_user_id'   => $sellerId,
                    'to_user_id'     => $buyerId,
                    'score'          => rand(1, 5),
                ]);
            }
        }
    }
}
