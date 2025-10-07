<?php

namespace App\UseCases\Adapters;

use App\Models\User;

use App\UseCases\Ports\IAuthUseCases;

use App\Services\Ports\IEmailService;

use App\Repositories\Ports\IUserRepository;

use Illuminate\Support\Facades\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

use App\Http\Exceptions\EmailAlreadyVerifiedException;

class AuthUseCases implements IAuthUseCases
{
    protected IEmailService $emailService;
    protected IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository, IEmailService $emailService)
    {
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
    }

    public function register(array $data): void
    {
        $user = $this->userRepository->save($data);

        $this->emailService->sendVerification($user);

        Auth::login($user);
    }

    public function login(array $credentials): User
    {
        if (!Auth::attempt($credentials)) {
            throw new AuthenticationException();
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            throw new AuthorizationException();
        }

        return $user;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function resendEmailVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            throw new EmailAlreadyVerifiedException();
        }

        $user->sendEmailVerificationNotification();
    }
}
