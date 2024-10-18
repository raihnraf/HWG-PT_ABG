<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookCopy;

class BookCopySeeder extends Seeder
{
    public function run()
    {
        Book::all()->each(function ($book) {
            for ($i = 0; $i < $book->total_copies; $i++) {
                BookCopy::create([
                    'book_id' => $book->id,
                    'status' => 'available'
                ]);
            }
        });
    }
}
