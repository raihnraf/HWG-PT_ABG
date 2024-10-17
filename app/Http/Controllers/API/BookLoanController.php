<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookLoanRequest;
use App\Http\Resources\BookLoanResource;
use App\Models\Book;
use App\Models\BookLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookLoanController extends Controller
{
    public function index()
    {
        $bookLoans = BookLoan::with(['user', 'book'])->where('user_id', Auth::id())->get();
        return BookLoanResource::collection($bookLoans);
    }

    public function store(BookLoanRequest $request)
    {
        $book = Book::findOrFail($request->book_id);
        
        if ($book->available_copies <= 0) {
            return response()->json(['message' => 'No copies available for borrowing'], 400);
        }

        $bookLoan = BookLoan::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'borrowed_at' => now(),
            'due_date' => $request->due_date,
            'status' => 'borrowed',
        ]);

        $book->decrement('available_copies');

        return new BookLoanResource($bookLoan);
    }

    public function return(BookLoan $bookLoan)
    {
        if ($bookLoan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($bookLoan->status === 'returned') {
            return response()->json(['message' => 'Book already returned'], 400);
        }

        $bookLoan->update([
            'returned_at' => now(),
            'status' => 'returned',
        ]);

        $bookLoan->book->increment('available_copies');

        return new BookLoanResource($bookLoan);
    }

    public function show(BookLoan $bookLoan)
    {
        if ($bookLoan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new BookLoanResource($bookLoan);
    }
}
