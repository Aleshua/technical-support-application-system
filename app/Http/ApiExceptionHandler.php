<?php

namespace App\Http;

use Illuminate\Foundation\Configuration\Exceptions;

use Log;
use Throwable;
use PDOException;

use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ApiExceptionHandler
{
    public static function processing(Exceptions $exceptions)
    {
        $exceptions->render(function (ValidationException $e) {
            return ApiResponses::errors(ApiMessages::VALIDATION_FAILED, 422, $e->errors());
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponses::message(ApiMessages::AUTHENTICATION_EXCEPTION, 401);
        });

        $exceptions->render(function (AuthorizationException $e) {
            return ApiResponses::message(ApiMessages::AUTHORIZATION_EXCEPTION, 403);
        });

        $exceptions->render(function (ModelNotFoundException $e) {
            return ApiResponses::message(ApiMessages::MODEL_NOT_FOUND, 404);
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            return ApiResponses::message(ApiMessages::ROUTE_NOT_FOUND, 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e) {
            return ApiResponses::message(ApiMessages::METHOD_NOT_ALLOWED, 405);
        });

        $exceptions->render(function (TokenMismatchException $e) {
            return ApiResponses::message(ApiMessages::CSRF_TOKEN_MISMATCH, 419);
        });

        $exceptions->render(function (InvalidSignatureException $e) {
            return ApiResponses::message(ApiMessages::INVALID_SIGNATURE, 403);
        });

        $exceptions->render(function (QueryException $e) {

            $sqlState = $e->getCode();
            $message = $e->getMessage();

            switch ($sqlState) {
                case '23505':
                    $field = null;

                    if (preg_match('/constraint "(.+?)"/', $message, $matches)) {
                        $indexName = $matches[1];
                        $parts = explode('_', $indexName);
                        if (count($parts) >= 2) {
                            $field = $parts[count($parts) - 2];
                        } else {
                            $field = $indexName;
                        }
                    }

                    return ApiResponses::message(
                        "Поле {$field} уже занято",
                        422,
                    );
            }

            Log::error($e->getMessage());

            return ApiResponses::message(ApiMessages::SERVER_ERROR, 500);
        });

        $exceptions->render(function (PDOException $e) {

            Log::error($e->getMessage());
            
            return ApiResponses::message(ApiMessages::SERVER_ERROR, 500);
        });

        $exceptions->render(function (Throwable $e) {

            Log::error($e->getMessage());

            return ApiResponses::message(ApiMessages::SERVER_ERROR, 500);
        });
    }
}
