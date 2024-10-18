<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function index()
    {
        return Cache::remember('books.all', 3600, function () {
            $books = Book::all();
            return BookResource::collection($books);
        });
    }

    public function store(BookRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // Ensure available_copies is set, defaulting to total_copies if not provided
            $validatedData['available_copies'] = $validatedData['available_copies'] ?? $validatedData['total_copies'];

            $book = Book::create($validatedData);
            
            if (!$book) {
                Log::error('Failed to create book', $validatedData);
                return response()->json(['error' => 'Failed to create book'], 500);
            }

            // Reload the book to ensure we have the latest data
            $book = $book->fresh();

            Log::info('Book created successfully', ['id' => $book->id, 'available_copies' => $book->available_copies]);

            return new BookResource($book);
        } catch (\Exception $e) {
            Log::error('Exception when creating book: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the book'], 500);
        }
    }

    public function show($id)
    {
        return Cache::remember('book.'.$id, 3600, function () use ($id) {
            $book = Book::find($id);
            
            if (!$book) {
                return response()->json(['message' => 'Book not found'], 404);
            }
            
            return new BookResource($book);
        });
    }

    public function update(BookRequest $request, $id)
    {
        $book = Book::findOrFail($id);
        $book->update($request->validated());
        return new BookResource($book);
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);

        Cache::forget('books.all');
        Cache::forget('book.'.$id);
    }
}
