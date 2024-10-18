<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddBookCopiesForExistingBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $books = \App\Models\Book::all();
        foreach ($books as $book) {
            for ($i = 0; $i < $book->total_copies; $i++) {
                \App\Models\BookCopy::create([
                    'book_id' => $book->id,
                    'status' => 'available'
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\BookCopy::truncate();
    }
}
