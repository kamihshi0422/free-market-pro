@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
@endsection

@section('content')
<section class="layout">
    <aside class="aside">
        <h1>その他の取引</h1>
        @foreach ($userTransactions as $transaction)
            @if ($transaction->id !== $currentTransaction->id)
                <a href="{{ route('transaction.show', $transaction->id) }}">
                    {{ $transaction->product->name }}
                </a>
            @endif
        @endforeach
    </aside>

    <section class="main">
        <header class="header">
            <div class="profile-box">
                <img src="{{ asset('storage/' . $otherUser->img_url) }}" alt="プロフィール画像">
                <h1>「{{ $otherUser->name }}」 さんとの取引画面</h1>
            </div>
            {{-- 購入者のみ表示 --}}
            @if (
                auth()->id() === $currentTransaction->buyer_id &&
                !$currentTransaction->ratings->where('from_user_id', auth()->id())->count()
            )
                <button id="complete-btn" type="button" class="complete-btn">取引を完了する</button>
            @endif
        </header>

        <div class="product-box">
            <img src="{{ asset('storage/' . $currentTransaction->product->img_url) }}" alt="商品画像">
            <div class="product-detail">
                <p class="product-name">{{ $currentTransaction->product->name }}</p>
                <p class="product-price">¥{{ number_format($currentTransaction->product->price) }}</p>
            </div>
        </div>

        <div class="message-wrapper">
            {{-- メッセージ一覧 --}}
            @foreach ($currentTransaction->messages as $message)
                <div class="message-box {{ $message->user_id === auth()->id() ? 'my-message' : 'other-message' }}">
                    <div class="message-user">
                        <img src="{{ asset('storage/' . $message->user->img_url) }}" alt="プロフィール画像">
                        <p class="user-name">{{ $message->user->name }}</p>
                    </div>

                    <div class="message-content">
                        <p class="message-text">{{ $message->message }}</p>

                        @if ($message->image_path)
                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="添付画像">
                        @endif
                    </div>

                    @if ($message->user_id === auth()->id())
                        <input type="checkbox" id="edit-{{ $message->id }}" class="edit-toggle">

                        <div class="message-actions">
                            <label for="edit-{{ $message->id }}" class="edit-btn">編集</label>

                            <form
                                action="{{ route('transaction.message.destroy', [$currentTransaction->id, $message->id]) }}"
                                method="POST"
                                onsubmit="return confirm('このメッセージを削除しますか？');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">削除</button>
                            </form>
                        </div>

                        <form
                            action="{{ route('transaction.message.update', [$currentTransaction->id, $message->id]) }}"
                            method="POST"
                            class="inline-edit-form"
                        >
                            @csrf
                            @method('PUT')
                            <textarea name="message">{{ $message->message }}</textarea>
                            <div class="inline-edit-actions">
                                <button type="submit">保存</button>
                                <label for="edit-{{ $message->id }}">キャンセル</label>
                            </div>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="submit-box">
            {{-- エラーメッセージ --}}
            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('transaction.message.store', $currentTransaction->id) }}" method="POST" enctype="multipart/form-data" class="submit-form">
                @csrf

                <textarea id="message" name="message" placeholder="取引メッセージを記入してください">{{ old('message', $draftMessage) }}</textarea>

                <input type="file" name="image" id="image" accept=".png,.jpeg">
                <label for="image" class="image-add-btn">画像を追加</label>

                <button type="submit" class="submit-btn">
                    <img src="{{ asset('storage/products_images/inputButton.jpg') }}" alt="送信">
                </button>
            </form>
        </div>
    </section>

    {{-- 取引完了モーダル --}}
    <div id="complete-modal" class="complete-modal hidden">
        <div class="complete-modal-content">

            <p class="complete-title">取引が完了しました。</p>
            <p class="complete-subtitle">今回の取引相手はどうでしたか？</p>

            <form method="POST" action="{{ $completeActionRoute }}">
                @csrf

                <div class="rating">
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <input
                            type="radio"
                            name="score"
                            value="{{ $rating }}"
                            id="star{{ $rating }}"
                            required
                        >
                        <label for="star{{ $rating }}">★</label>
                    @endfor
                </div>

                <div class="submit-wrapper">
                    <button type="submit" class="complete-submit">送信する</button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- FN009：下書き保存 --}}
<script>
const textarea = document.getElementById('message');

if (textarea) {
    let timer = null;

    textarea.addEventListener('input', function () {
        clearTimeout(timer);

        timer = setTimeout(() => {
            fetch('{{ route("transaction.draft.store", $currentTransaction->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: textarea.value
                })
            });
        }, 1000);
    });
}
</script>

{{-- FN012：取引完了モーダル --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('complete-modal');
    const completeBtn = document.getElementById('complete-btn');

    if (completeBtn) {
        completeBtn.addEventListener('click', () => {
            modal?.classList.remove('hidden');
        });
    }

    @if ($showCompleteModal)
        modal?.classList.remove('hidden');
    @endif
});
</script>
@endsection