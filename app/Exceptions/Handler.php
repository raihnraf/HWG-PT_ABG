<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (!$e instanceof ValidationException) {
                Log::error('Uncaught exception: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            }
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return $this->handleApiException($request, $e);
            }
        });
    }

    private function handleApiException($request, Throwable $exception): JsonResponse
    {
        $statusCode = 500;
        $message = 'Internal Server Error';
        $error = 'Server Error';

        if ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'The given data was invalid.';
            $error = 'Validation Error';
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Unauthenticated. Please log in to access this resource.';
            $error = 'Unauthorized';
        } elseif ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = 'The requested resource was not found.';
            $error = 'Not Found';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $statusCode = 405;
            $message = 'The specified method for the request is invalid.';
            $error = 'Method Not Allowed';
        }

        return response()->json([
            'message' => $message,
            'error' => $error,
            'status_code' => $statusCode
        ], $statusCode);
    }
}
