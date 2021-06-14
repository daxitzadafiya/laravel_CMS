<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        $errors = $exception->errors();
        $errorsResponse = [];

        if (is_array($errors)) {
            foreach ($errors as $key => $value) {
                $errorsResponse[$key] = $value[0] ?? '';
            }
        }

        return response()->json([
            'error' => [
                'message' => $exception->getMessage(),
                'errors' => $errorsResponse,
            ],
        ], $exception->status);
    }
}
