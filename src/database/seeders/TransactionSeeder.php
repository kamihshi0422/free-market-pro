<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        Transaction::create([
            'buyer_id'   => 2,
            'seller_id'  => 1,
            'product_id'=> 2,
            'status'     => 0,
        ]);

        Transaction::create([
            'buyer_id'   => 2,
            'seller_id'  => 1,
            'product_id'=> 3,
            'status'     => 1,
        ]);

        Transaction::create([
            'buyer_id'   => 1,
            'seller_id'  => 2,
            'product_id'=> 6,
            'status'     => 0,
        ]);

        Transaction::create([
            'buyer_id'   => 1,
            'seller_id'  => 2,
            'product_id'=> 7,
            'status'     => 1,
        ]);
    }
}
