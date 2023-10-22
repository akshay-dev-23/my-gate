<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

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

        $this->renderable(function (Exception $exc, Request $request) {
            if ($request->is('api/*')) {
                // $message = '';
                // if ($exc instanceof \Illuminate\Auth\AuthenticationException) {
                //     $message = 'Unauthenticated.';
                //     $code = 401;
                // }
                // else if ($exc->getCode() == 0 || $exc->getCode() == 500 || $exc->getCode() == null) {
                //     $message = "Something went wrong. Please try again later.";
                //     $code = 500;
                // } else {
                //     $message = $exc->getMessage();
                //     $code = $exc->getCode();
                // }
                // return response()->json([
                //     'message' => $message,
                //     'data' => []
                // ], $code);
            }
        });
    }
}
