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
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
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

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException)
            return api()->validation(trans('responses.validation-error'), $e->errors());
        if ($this->isHttpException($e)) {
            switch ($e->getStatusCode()) {
                case '401':
                    return api()->error($e->getMessage() ?? trans('responses.login-again'), [], 401);
                    break;
                case '404':
                    return api()->error(trans('responses.not-found'), [], 404);
                    break;
                case '500':
                    return api()->error(trans('responses.something-went-wrong'), [], 500);
                    break;

                default:
                    return api()->error($e->getMessage(), [], $e->getStatusCode());
                    break;

            }
        }
        $code = (int)$e->getCode();
        return api()->error($e->getMessage(), [], ($code > 599 || $code < 100) ? 400 : $code);

    }
}
