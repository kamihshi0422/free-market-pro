<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    private function authorizeTransaction(Transaction $transaction): void
    {
        $userId = auth()->id();

        if ($transaction->buyer_id !== $userId && $transaction->seller_id !== $userId) {
            throw new AuthorizationException();
        }
    }

    private function draftKey(int $transactionId): string
    {
        return 'transaction_draft_' . $transactionId . '_' . auth()->id();
    }

    public function store(StoreMessageRequest $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $storedImagePath = null;
        if ($request->hasFile('image')) {
            $storedImagePath = $request->file('image')->store('messages', 'public');
        }

        Message::create([
            'transaction_id' => $transaction->id,
            'user_id'        => auth()->id(),
            'message'        => $request->message,
            'image_path'     => $storedImagePath,
            'is_read'        => false,
        ]);

        Cache::forget($this->draftKey($transaction->id));

        $transaction->touch();

        return redirect()->route('transaction.show', $transaction);
    }

    public function update(StoreMessageRequest $request, Transaction $transaction, Message $message)
    {
        $this->authorizeTransaction($transaction);

        if ($message->user_id !== auth()->id() || $message->transaction_id !== $transaction->id) {
            throw new AuthorizationException();
        }

        $message->update([
            'message' => $request->message,
        ]);

        $transaction->touch();

        return redirect()->route('transaction.show', $transaction);
    }

    public function destroy(Transaction $transaction, Message $message)
    {
        $this->authorizeTransaction($transaction);

        if ($message->user_id !== auth()->id() || $message->transaction_id !== $transaction->id) {
            throw new AuthorizationException();
        }

        $message->delete();
        $transaction->touch();

        return redirect()->route('transaction.show', $transaction);
    }

    public function storeDraft(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        Cache::put($this->draftKey($transaction->id), $request->message, now()->addMinutes(30));

        return response()->json(['status' => 'saved']);
    }
}
