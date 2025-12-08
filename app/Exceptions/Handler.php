<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                $response = [
                    'success' => false,
                    'message' => config('app.debug') ? $e->getMessage() : 'An error occurred',
                ];

                if (config('app.debug')) {
                    $response['error'] = class_basename($e);
                }

                return response()->json($response, $status);
            }
        });
    }
}
