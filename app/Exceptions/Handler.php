<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        DB::rollBack();
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json([
                'error' => 'Resource not found'
            ], 404);
        }
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => $exception->getMessage()
            ], 404);
        }
        if ($exception instanceof QueryException) {
            if ($exception->getCode() == "42S02") {
                return response()->json([
                    'error' => "Database Error: Table or View not found, if this error persists, please contact the Admin.",
                    'message' => $exception->getMessage(),
                ], 404);
            }
            if ($exception->getCode() == "23000") {
                return response()->json([
                    'error' => "Database Error",
                    'message' => $exception->getMessage(),
                ], 400);
            }
            return response()->json([
                'error' => "Database Error: if this error presists, please contact the Admin.",
                'message' => $exception->getMessage() . " Code: " . $exception->getCode()
            ], 400);

        }

        if ($exception instanceof \BadMethodCallException)
        {
            return response()->json([
                'error' => "Eloquent Error: Bad Method Exception!",
                'message' => $exception->getMessage()
            ], 400);
        }
        if ($exception instanceof AccessDeniedHttpException)
        {
            return response()->json([
                'error' => "Access Denied!",
                'message' => $exception->getMessage()
            ], 401);
        }
        if ($exception instanceof RelationNotFoundException)
        {
            return response()->json([
                'error' => "Database Error: Relationship not found, if this error persists, please contact the Admin. ",
                'message' => $exception->getMessage() . "Code: " .$exception->getCode()
            ],400);
        }
        if ($exception instanceof MethodNotAllowedHttpException)
        {
            return response()->json([
                'error' => "Method Not Allowed: Check the method (Get, Post ..). please contact the Admin. ",
                'message' => $exception->getMessage(). "Code: ". $exception->getCode()
            ],400);
        }
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }
}
