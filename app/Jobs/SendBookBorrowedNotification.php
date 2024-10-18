<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BookLoan;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookBorrowedNotification;

class SendBookBorrowedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bookLoan;

    /**
     * Create a new job instance.
     */
    public function __construct(BookLoan $bookLoan)
    {
        $this->bookLoan = $bookLoan;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->bookLoan->user->email)->send(new BookBorrowedNotification($this->bookLoan));
    }
}
