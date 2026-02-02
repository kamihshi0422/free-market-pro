<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use App\Mail\TransactionCompletedMail;
use Illuminate\Support\Facades\Mail;

class TransactionCompletionController extends Controller
{
    private function authorizeTransaction(Transaction $transaction): void
    {
        $userId = auth()->id();

        if ($transaction->buyer_id !== $userId && $transaction->seller_id !== $userId) {
            throw new AuthorizationException();
        }
    }

    public function completeByBuyer(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        if ($transaction->buyer_id !== auth()->id()) {
            throw new AuthorizationException();
        }

        $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        DB::transaction(function () use ($transaction, $request) {
            Rating::create([
                'transaction_id' => $transaction->id,
                'from_user_id'   => auth()->id(),
                'to_user_id'     => $transaction->seller_id,
                'score'          => $request->score,
            ]);

            $transaction->update(['status' => 1]);
        });

        Mail::to($transaction->seller->email)->send(new TransactionCompletedMail($transaction));

        return redirect()->route('top.show');
    }

    public function completeBySeller(Request $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        if (auth()->id() !== $transaction->seller_id) {
            abort(403);
        }

        $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        $alreadyRated = Rating::where('transaction_id', $transaction->id)
            ->where('from_user_id', auth()->id())
            ->exists();

        if ($alreadyRated) {
            return redirect()->route('top.show');
        }

        Rating::create([
            'transaction_id' => $transaction->id,
            'from_user_id'   => auth()->id(),
            'to_user_id'     => $transaction->buyer_id,
            'score'          => $request->score,
        ]);

        return redirect()->route('top.show');
    }
}
