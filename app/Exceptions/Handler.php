<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Str;
use App\ApiHelper as ResponseInterface;
use App\Constants\ErrorCode;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    public function error_code() { return strtoupper(Str::random(10)); }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public static function generateReport($exception,$code)
    {
        if ($exception){
            Log::error(
                'Error ID '.$code."\n".
                'Message : '.$exception->getMessage()."\n".
                'File : '.$exception->getFile()."\n".
                'Line : '.$exception->getLine()."\n".
                'Trace : '. "\n" . $exception->getTraceAsString()."\n"
            );
        }
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function render($request, Throwable $exception)
    {
        if ($request->header('content-type') == 'application/json') {
            return $this->handleApiException($request, $exception);
        }else {
            $retval = parent::render($request, $exception);
        }
        return $retval;
    }

    private function handleApiException($request, \Exception $exception)
    {
        $code = $this->error_code();
        $exception = $this->prepareException($exception);

        if ($exception instanceof HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        self::generateReport($exception,$code);
        return ResponseInterface::errorResponse(ErrorCode::INTERNAL_ERROR_SERVER,[
            "code" => $code,
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
            "trace" => $exception->getTraceAsString()
        ]);
    }

}
