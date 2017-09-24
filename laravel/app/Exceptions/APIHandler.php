<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class APIHandler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthenticationException) {
            $e = new HttpException(401, $e->getMessage());
        } elseif ($e instanceof AuthorizationException) {
            $e = new HttpException(403, $e->getMessage());
        } elseif ($e instanceof NotFoundHttpException) {
            $e = new HttpException(404, $e->getMessage());
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            return $e->getResponse();
        }
        
        if ($this->isHttpException($e)) {
            switch ($e->getStatusCode()) {
                case '401':
                    return response()->json([
                        'response' => 'Please login',
                        'status' => 401,
                        'API_version' => '1.0'
                    ], 401);
                    break;
                
                // not authorized
                case '403':
                    return response()->json([
                        'response' => 'You do not have permission to do this',
                        'status' => 403,
                        'API_version' => '1.0'
                    ], 403);
                    break;
                    
                    // not found
                case '404':
                    return response()->json([
                        'response' => 'API route does not exist',
                        'status' => 404,
                        'API_version' => '1.0'
                    ], 404);
                    break;
                    
                    // internal error
                case '500':
                    return response()->json([
                        'response' => 'Incorrect API call',
                        'status' => 500,
                        'API_version' => '1.0'
                    ], 500);
                    break;
                    
                default:
                    return $this->renderHttpException($e);
                    break;
            }
        } else {
            return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e);
        }
    }
}
