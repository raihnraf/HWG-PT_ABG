<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\BookLoanController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Books
    Route::apiResource('books', BookController::class);

    // Book Loans
    Route::get('/book-loans', [BookLoanController::class, 'index']);
    Route::post('/book-loans', [BookLoanController::class, 'store']);
    Route::get('/book-loans/{bookLoan}', [BookLoanController::class, 'show']);
    Route::post('/book-loans/{bookLoan}/return', [BookLoanController::class, 'return']);

    Route::post('email/verification-notification', [VerificationController::class, 'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
});

