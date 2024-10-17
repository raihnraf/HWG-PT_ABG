<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookLoanRequest;
use App\Http\Resources\BookLoanResource;
use App\Models\Book;
use App\Models\BookLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookLoanController extends Controller
{
    public function index()
    {
        Log::info('BookLoanController@index method called');

        try {
            $bookLoans = BookLoan::with(['user', 'book'])->where('user_id', Auth::id())->get();

            if ($bookLoans->isEmpty()) {
                Log::info('No book loans found for user', ['user_id' => Auth::id()]);
                return response()->json([
                    'message' => 'No book loans found',
                    'data' => []
                ], 404);
            }

            Log::info('Book loans retrieved successfully', ['count' => $bookLoans->count()]);
            return BookLoanResource::collection($bookLoans);
        } catch (\Exception $e) {
            Log::error('Exception when retrieving book loans: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'An error occurred while retrieving book loans',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function return($id)
    {
        $bookLoan = BookLoan::findOrFail($id);
        Log::info('Return method called for book loan: ' . $bookLoan->id);
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
