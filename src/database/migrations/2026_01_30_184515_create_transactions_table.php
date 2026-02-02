<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // 商品
            $table->foreignId('product_id')
                    ->constrained()
                    ->cascadeOnDelete();
            // 購入者
            $table->foreignId('buyer_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            // 出品者
            $table->foreignId('seller_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            // 取引状態（0:取引中 / 1:完了）
            $table->tinyInteger('status')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
}
