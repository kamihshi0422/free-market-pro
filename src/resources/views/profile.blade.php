@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="mypage-head">
    <div class="mypage-head-inner">
        <div class="user">
            <div class="user-img">
                @if(!empty($user->img_url))
                    <img src="{{ asset('storage/' . $user->img_url) }}" alt="">
                @else
                    <div class="user-placeholder"></div>
                @endif
            </div>

            <div class="user-info">
                <h1 class="user-name">{{ $user->name }}</h1>
                {{-- ユーザー評価 --}}
                @if($ratingStats->count > 0)
                    <div class="user-rating">
                        <p>
                            @for ($rating = 1; $rating <= 5; $rating++)
                                @if ($rating <= $ratingStats->average)
                                    <span class="star-filled">★</span>
                                @else
                                    <span class="star-empty">★</span>
                                @endif
                            @endfor
                        </p>
                    </div>
                @endif
            </div>
        </div>
        <a class="profile-edit-btn" href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>
</div>

<div class="mypage-nav">
    <a href="{{ route('profile.show', ['page' => 'sell']) }}" class="{{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ route('profile.show', ['page' => 'buy']) }}" class="{{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
    {{-- 取引中の商品 ②など取引メッセージ数を表示 --}}
    <a href="{{ route('profile.show', ['page' => 'transaction']) }}"class="transaction-link {{ $page === 'transaction' ? 'active' : '' }}">
        <span class="transaction-text">取引中の商品</span>

        @if($totalUnreadMessages > 0)
            <span class="badge">{{ $totalUnreadMessages }}</span>
        @endif
    </a>
</div>

<section class="product-list">
    @if($page === 'sell')
        @foreach($myProducts as $product)
        <div class="product-card">
            <a href="{{ route('detail.show', $product->id) }}">
                <img class="product-img" src="{{ asset('storage/' . $product->img_url) }}" alt="商品画像">
            </a>
            <div class="product-detail">
                <p>{{ $product->name }}</p>
                @if($product->purchase)
                    <span class="sold-label">Sold</span>
                @endif
            </div>
        </div>
        @endforeach
    @elseif($page === 'buy')
        @foreach($purchases as $purchase)
        <div class="product-card">
            <a href="{{ route('detail.show', $purchase->product->id) }}">
                <img class="product-img" src="{{ asset('storage/' . $purchase->product->img_url) }}" alt="商品画像">
            </a>
            <div class="product-detail">
                <p>{{ $purchase->product->name }}</p>
            </div>
        </div>
        @endforeach
    @endif

    @if($page === 'transaction')
        @foreach($transactions as $transaction)
            <div class="product-card">
                <a href="{{ route('transaction.show', $transaction->id) }}">
                    <div class="img-wrapper">
                        @if($transaction->unread_messages_count > 0)
                            <span class="unread-badge">
                                {{ $transaction->unread_messages_count }}
                            </span>
                        @endif
                        <img
                            class="product-img"
                            src="{{ asset('storage/' . $transaction->product->img_url) }}"
                            alt="商品画像"
                        >
                    </div>
                </a>
                <div class="product-detail">
                    <p>{{ $transaction->product->name }}</p>
                </div>
            </div>
        @endforeach
    @endif
</section>
@endsection