<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Access\AuthorizationException;

class TransactionController extends Controller
{
    private function authorizeTransaction(Transaction $transaction): void
    {
        $userId = auth()->id();

        if (
            $transaction->buyer_id !== $userId &&
            $transaction->seller_id !== $userId
        ) {
            throw new AuthorizationException();
        }
    }

    private function draftKey(int $transactionId): string
    {
        return 'transaction_draft_' . $transactionId . '_' . auth()->id();
    }

    public function showTransaction(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $loginUserId = auth()->id();

        Message::where('transaction_id', $transaction->id)
            ->where('user_id', '!=', $loginUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $currentTransaction = Transaction::with([
            'messages.user',
            'product',
            'ratings',
            'buyer',
            'seller',
        ])->findOrFail($transaction->id);

        $userTransactions = Transaction::pendingForUser($loginUserId)
            ->with('product')
            ->orderByDesc('updated_at')
            ->get();

        $draftMessage = Cache::get(
            $this->draftKey($transaction->id)
        );

        $otherUser = $currentTransaction->buyer_id === $loginUserId
            ? $currentTransaction->seller
            : $currentTransaction->buyer;

        $buyerRated = $currentTransaction->ratings
            ->where('from_user_id', $currentTransaction->buyer_id)
            ->count() > 0;

        $sellerRated = $currentTransaction->ratings
            ->where('from_user_id', $currentTransaction->seller_id)
            ->count() > 0;

        $showCompleteModal = false;

        if (
            $loginUserId === $currentTransaction->seller_id &&
            $currentTransaction->status === 1 &&
            $buyerRated &&
            !$sellerRated
        ) {
            $showCompleteModal = true;
        }

        if (
            $loginUserId === $currentTransaction->seller_id &&
            $currentTransaction->status === 1 &&
            $buyerRated &&
            !$sellerRated
        ) {
            $showCompleteModal = true;
        }

        $completeActionRoute = null;

        if ($loginUserId === $currentTransaction->buyer_id) {
            $completeActionRoute = route('transaction.complete.buyer', $currentTransaction->id);
        }

        if ($loginUserId === $currentTransaction->seller_id) {
            $completeActionRoute = route('transaction.complete.seller', $currentTransaction->id);
        }

        return view('transaction', [
            'currentTransaction'  => $currentTransaction,
            'otherUser'           => $otherUser,
            'userTransactions'    => $userTransactions,
            'draftMessage'        => $draftMessage,
            'showCompleteModal'   => $showCompleteModal,
            'completeActionRoute' => $completeActionRoute,
        ]);
    }
}