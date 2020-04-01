<?php

namespace App\Exceptions;

use Exception;
use ErrorException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Log;

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
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            return response()->json([
                'error' => $exception->getMessage(),
                'code' => $statusCode
            ], $statusCode);
        }

        if ($exception instanceof ModelNotFoundException) {
            $statusCode = 404;
            return response()->json([
                'error' => 'not found',
                'code' => $statusCode
            ], $statusCode);
        }

        if ($exception instanceof ErrorException) {
            $statusCode = 500;
            return response()->json([
                'error' => 'internal error',
                'code' => $statusCode
            ], $statusCode);
        }

        Log::info('exception=' . $exception);
        return parent::render($request, $exception);
    }
}
