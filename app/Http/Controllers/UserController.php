<?php

namespace App\Http\Controllers;

use App\Http\ApiMessages;
use App\Http\ApiResponses;
use App\Http\Controllers\Controller;

use App\UseCases\Ports\IUserUseCases;

use App\Http\DTO\Requests\UserUpdateMeRequest;
use App\Http\DTO\Requests\UserUpdateRoleRequest;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected IUserUseCases $userUseCases;

    public function __construct(IUserUseCases $userUseCases)
    {
        $this->userUseCases = $userUseCases;
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->userUseCases->findUser(Auth::id());

        return ApiResponses::data('', 200, $user);
    }

    public function updateMe(UserUpdateMeRequest $request): JsonResponse
    {
        $this->userUseCases->updateUser(Auth::id(), $request->validated());

        return ApiResponses::message(ApiMessages::ENTITY_UPDATED, 200);
    }

    public function deleteMe(Request $request): JsonResponse
    {
        $this->userUseCases->deleteUser(Auth::id());

        return ApiResponses::message(ApiMessages::ENTITY_DELETED, 200);
    }

    public function updateRole(UserUpdateRoleRequest $request, int $userId): JsonResponse
    {
        $this->userUseCases->updateUser($userId, $request->validated());
        
        return ApiResponses::message(ApiMessages::ENTITY_UPDATED, 200);
    }

}
