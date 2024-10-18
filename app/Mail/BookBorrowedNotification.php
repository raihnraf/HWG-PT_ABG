<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BookLoan;

class BookBorrowedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bookLoan;

    public function __construct(BookLoan $bookLoan)
    {
        $this->bookLoan = $bookLoan;
    }

    public function build()
    {
        return $this->view('emails.book_borrowed')
                    ->subject('Book Borrowed Notification');
    }
}
