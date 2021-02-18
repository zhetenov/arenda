<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterSellerRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ValidatePhoneRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Services\DTO\User\RegisterSellerDTO;
use App\Services\DTO\ValidateAndSendCodeDTO;
use App\Services\Handlers\User\RegisterSellerHandler;
use App\Services\Handlers\User\RegisterUserHandler;
use App\Services\Handlers\User\VerifyCodeHandler;
use App\Services\Handlers\ValidateAndSendCode\ValidateAndSendCodeHandler;
use App\Services\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * @param RegisterUserRequest $request
     * @param RegisterUserHandler $handler
     * @return JsonResponse
     * @throws \App\Exceptions\NotVerifiedPhone
     */
    public function registerUser(RegisterUserRequest $request, RegisterUserHandler $handler): JsonResponse
    {
        $user = $handler->handle($request->getDTO());
        return $this->response('Успешная регистрация', new UserResource($user));
    }

    /**
     * @param RegisterSellerRequest $request
     * @param RegisterSellerHandler $handler
     * @return JsonResponse
     * @throws \App\Exceptions\NotVerifiedPhone
     */
    public function registerSeller(RegisterSellerRequest $request, RegisterSellerHandler $handler): JsonResponse
    {
        $user = $handler->handle(RegisterSellerDTO::fromArray($request->all()));
        return $this->response('Успешная регистрация', new UserResource($user));
    }

    public function auth(AuthRequest $request)
    {

    }

    /**
     * @param ValidatePhoneRequest $request
     * @param ValidateAndSendCodeHandler $handler
     * @return JsonResponse
     */
    public function validatePhone(ValidatePhoneRequest $request, ValidateAndSendCodeHandler $handler): JsonResponse
    {
        $handler->handle(ValidateAndSendCodeDTO::fromArray([
            'phone' => $request->get('phone'),
        ]));
        return $this->response('Смс отправлено успешно', true);
    }

    /**
     * @param VerifyCodeRequest $request
     * @param VerifyCodeHandler $handler
     * @return JsonResponse
     */
    public function verifyCode(VerifyCodeRequest $request, VerifyCodeHandler $handler): JsonResponse
    {
        $result = $handler->handle($request->get('phone'), $request->get('code'));
        return $this->response('Принято', $result);
    }
}