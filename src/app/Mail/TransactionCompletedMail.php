<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    public function __construct(public Transaction $transaction) {}

    public function build()
    {
        return $this
            ->subject('取引が完了しました')
            ->view('emails.transaction_completed');
    }
}
