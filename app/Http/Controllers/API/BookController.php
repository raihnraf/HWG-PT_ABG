<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->get();
        return BookResource::collection($books);
    }

    public function store(BookRequest $request)
    {
        Log::info('BookController@store method called');
        Log::info('Request data:', $request->all());

        try {
            $validatedData = $request->validated();
            Log::info('Validated data:', $validatedData);

            $book = Book::create($validatedData);
            
            if ($book) {
                Log::info('Book created successfully', ['id' => $book->id, 'title' => $book->title]);
                return new BookResource($book);
            } else {
                Log::error('Failed to create book');
                return response()->json(['error' => 'Failed to create book'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception when creating book: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while creating the book: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $book = Book::find($id);
        
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        
        return new BookResource($book);
    }

    public function update(BookRequest $request, $id)
    {
        Log::info('BookController@update method called for book ID: ' . $id);
        Log::info('Request data:', $request->all());

        try {
            $book = Book::findOrFail($id);
            $validatedData = $request->validated();
            Log::info('Validated data:', $validatedData);

            $book->update($validatedData);
            
            Log::info('Book updated successfully', ['id' => $book->id, 'title' => $book->title]);
            return new BookResource($book);
        } catch (ValidationException $e) {
            Log::warning('Validation failed when updating book', [
                'id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Exception when updating book: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'An error occurred while updating the book.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);
    }
}
