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
use App\Jobs\SendBookBorrowedNotification; 
use Illuminate\Support\Facades\DB;
use App\Models\Role;

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
        try {
            $book = Book::findOrFail($request->book_id);
            Log::info('Book copies:', ['copies' => $book->copies()->get()]);

            if ($book->available_copies <= 0) {
                return response()->json(['message' => 'No copies available for this book'], 400);
            }

            $bookLoan = null;

            DB::transaction(function () use ($request, $book, &$bookLoan) {
                // Find an available book copy
                $bookCopy = $book->copies()->where('status', 'available')->first();

                if (!$bookCopy) {
                    throw new \Exception('No available copies found for this book');
                }

                $bookLoan = BookLoan::create([
                    'user_id' => Auth::id(),
                    'book_copy_id' => $bookCopy->id,
                    'borrowed_at' => now(),
                    'due_date' => $request->due_date,
                    'status' => 'borrowed'
                ]);

                $bookCopy->update(['status' => 'borrowed']);
                $book->decrement('available_copies');
            });

            if (!$bookLoan) {
                return response()->json(['message' => 'Failed to create book loan'], 500);
            }

            return new BookLoanResource($bookLoan);

        } catch (\Exception $e) {
            Log::error('Error in BookLoanController@store: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function return($id)
    {
        $bookLoan = BookLoan::findOrFail($id);

        if ($bookLoan->status === 'returned') {
            return response()->json(['message' => 'Book already returned'], 400);
        }

        DB::transaction(function () use ($bookLoan) {
            $bookLoan->update([
                'returned_at' => now(),
                'status' => 'returned',
            ]);

            $bookLoan->bookCopy->update(['status' => 'available']);
        });

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
