<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postcode',
        'address',
        'building',
        'img_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

     // リレーション
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function mylists()
    {
        return $this->belongsToMany(Product::class, 'mylists')->withTimestamps();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

        // 出品した商品
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // 購入した取引（購入者）
    public function buyTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    // 販売した取引（出品者）
    public function sellTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    // メッセージ
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
