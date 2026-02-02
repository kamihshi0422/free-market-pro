<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // どの取引のメッセージか
            $table->foreignId('transaction_id')
                    ->constrained()
                    ->cascadeOnDelete();

            // 送信者
            $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

            // メッセージ本文
            $table->text('message');
            $table->string('image_path')->nullable();

            // 既読フラグ
            $table->boolean('is_read')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
}
