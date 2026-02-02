<p>
    {{ $transaction->buyer->name }} さんが<br>
    「{{ $transaction->product->name }}」の取引を完了しました。
</p>

<p>
    下記リンクから取引画面を開き、評価を行ってください。
</p>

<p>
    <a href="{{ route('transaction.show', $transaction->id) }}">
        取引画面を開く
    </a>
</p>