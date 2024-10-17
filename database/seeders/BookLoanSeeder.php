<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookLoan;
use App\Models\User;
use App\Models\Book;

class BookLoanSeeder extends Seeder
{
    public function run()
    {
        $user = User::first() ?? User::factory()->create();
        $book = Book::first() ?? Book::factory()->create();

        BookLoan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'borrowed'
        ]);
    }
}
