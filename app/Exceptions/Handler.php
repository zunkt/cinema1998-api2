<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $classExp = get_class($exception);

        if($classExp == 'Illuminate\Auth\AuthenticationException'){
            return response()->json([
                'code' => 401,
                'data' => (object)[],
                'message' => '이 작업을 수행하려면 로그인하십시오.',//Please login to perform this action
                'errors' => ''
            ], 401);
        }else if($classExp == 'Twilio\Exceptions\RestException'){
            return response()->json([
                'code' => 422,
                'data' => (object)[],
                'message' => '전화 번호가 유효하지 않습니다',//Your phone number is not valid
                'errors' => ''
            ], 422);
        }

        return parent::render($request, $exception);
    }
}
