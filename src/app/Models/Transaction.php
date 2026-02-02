<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'buyer_id',
        'seller_id',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
}

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function unreadMessages()
    {
        return $this->messages()
            ->where('is_read', false)
            ->where('user_id', '!=', auth()->id());
    }

    public function scopePendingForUser($query, $userId)
    {
        return $query->where(function ($transactionQuery) use ($userId) {

            // 出品者としての取引
            $transactionQuery->where(function ($sellerQuery) use ($userId) {
                $sellerQuery->where('seller_id', $userId)
                            ->where(function ($statusQuery) use ($userId) {
                                $statusQuery->where('status', 0)
                                            ->orWhere(function ($completedQuery) use ($userId) {
                                                $completedQuery->where('status', 1)
                                                            ->whereDoesntHave('ratings', function ($ratingQuery) use ($userId) {
                                                                $ratingQuery->where('from_user_id', $userId);
                                                            });
                                            });
                            });
            })

            // 購入者としての取引
            ->orWhere(function ($buyerQuery) use ($userId) {
                $buyerQuery->where('buyer_id', $userId)
                        ->where(function ($statusQuery) use ($userId) {
                            $statusQuery->where('status', 0)
                                        ->orWhere(function ($completedQuery) use ($userId) {
                                            $completedQuery->where('status', 1)
                                                            ->whereDoesntHave('ratings', function ($ratingQuery) use ($userId) {
                                                                $ratingQuery->where('from_user_id', $userId);
                                                            });
                                        });
                        });
            });
        });
}
}

