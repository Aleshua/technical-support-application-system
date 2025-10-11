<?php

namespace App\Http\Controllers;

use App\Http\ApiResponses;
use App\Http\ApiMessages;
use App\Http\Controllers\Controller;

use App\UseCases\Ports\IAuthUseCases;

use App\Http\DTO\Requests\LoginRequest;
use App\Http\DTO\Requests\RegisterRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected IAuthUseCases $authUseCases;

    public function __construct(IAuthUseCases $authUseCases)
    {
        $this->authUseCases = $authUseCases;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authUseCases->register($request->validated());

        $request->session()->regenerate();

        return ApiResponses::message(
            ApiMessages::USER_REGISTERED,
            201,
        );
    }

    public function registerForm(Request $request): JsonResponse
    {
        return ApiResponses::data(
            "",
            200,
            [
                'csrf_token' => $request->session()->token(),
            ]
        );
    }

    public function confirmEmail(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return ApiResponses::message(
            ApiMessages::EMAIL_VERIFIED,
            200,
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authUseCases->login($request->validated());

        return ApiResponses::data(
            '',
            200,
            [
                'user' => $user,
                'csrf_token' => $request->session()->token()
            ]
        );
    }

    public function loginForm(Request $request): JsonResponse
    {
        return ApiResponses::data(
            "",
            200,
            [
                'csrf_token' => $request->session()->token(),
            ]
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authUseCases->logout();

        $request->session()->flush();

        return ApiResponses::message(
            ApiMessages::LOGOUT,
            200,
        );
    }

    public function resendEmailVerification(Request $request): JsonResponse
    {
        $this->authUseCases->resendEmailVerification();

        return ApiResponses::message(
            ApiMessages::EMAIL_RESENDED,
            200,
        );
    }
}
