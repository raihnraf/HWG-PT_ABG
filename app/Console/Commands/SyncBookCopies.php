<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;

class SyncBookCopies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:sync-copies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync book copies with total_copies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $books = Book::all();
        foreach ($books as $book) {
            $existingCopies = $book->copies()->count();
            $copiesToCreate = $book->total_copies - $existingCopies;

            if ($copiesToCreate > 0) {
                for ($i = 0; $i < $copiesToCreate; $i++) {
                    $book->copies()->create(['status' => 'available']);
                }
            }

            $book->update(['available_copies' => $book->copies()->where('status', 'available')->count()]);
            $this->info("Synced copies for book ID {$book->id}");
        }

        $this->info('All books have been synced with their copies.');
    }
}
