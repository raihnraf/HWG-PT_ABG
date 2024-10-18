<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    protected $fillable = ['book_id', 'status'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookLoans()
    {
        return $this->hasMany(BookLoan::class);
    }
}
