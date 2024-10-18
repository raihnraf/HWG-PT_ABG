<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('book_loans', function (Blueprint $table) {
            // Remove book_id if it exists
            if (Schema::hasColumn('book_loans', 'book_id')) {
                $table->dropForeign(['book_id']);
                $table->dropColumn('book_id');
            }
    
            // Add book_copy_id if it doesn't exist
            if (!Schema::hasColumn('book_loans', 'book_copy_id')) {
                $table->foreignId('book_copy_id')->constrained()->onDelete('cascade');
            }
        });
    }
    
    public function down()
    {
        Schema::table('book_loans', function (Blueprint $table) {
            $table->dropForeign(['book_copy_id']);
            $table->dropColumn('book_copy_id');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
        });
    }
};
