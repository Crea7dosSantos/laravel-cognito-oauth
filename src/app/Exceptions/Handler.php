<?php

namespace App\Exceptions;

use App\Models\Domain;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

    public function render($request, $exception)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $domain = new Domain(url()->current());
        $domain_part = $domain->getPart();

        if (strpos($domain_part, 'api.') !== false) {
            Log::debug('api exception error in Handler class');

            if ($this->isHttpException($exception)) {
                return response()->json([
                    'message' => $exception->getMessage()
                ], $exception->getStatusCode());
            }
            // HTTPエラー以外のエラー。400(Bad Request)を返す
            return response()->json([
                'message' => 'hoge'
            ], Response::HTTP_BAD_REQUEST);
        } else {
            Log::debug('web exception error in Handler class');
        }

        return parent::render($request, $exception);
    }
}
