<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\RegisterSellerRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ValidatePhoneRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\DTO\User\RegisterSellerDTO;
use App\Services\DTO\ValidateAndSendCodeDTO;
use App\Services\Handlers\User\EditProfileHandler;
use App\Services\Handlers\User\RegisterSellerHandler;
use App\Services\Handlers\User\RegisterUserHandler;
use App\Services\Handlers\User\VerifyCodeHandler;
use App\Services\Handlers\ValidateAndSendCodeChange\ValidateAndSendCodeChangeHandler;
use App\Services\Handlers\ValidateAndSendCode\ValidateAndSendCodeHandler;
use App\Services\Traits\ConstructionHelper;
use App\Services\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AuthController
 * @package App\Http\Controllers\V1\Auth
 */
final class AuthController extends Controller
{
    use ResponseTrait, ConstructionHelper;

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
     
    /**
     * @param VerifyCodeRequest $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        User::wherePhone($this->getNormalPhone($request->get('phone')))->update([
            'password' => $request->get('password'),
        ]);
        return $this->response('Изменено');
    }   

    /**
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function auth(AuthRequest $request): JsonResponse
    {
        $user = User::wherePhone($this->getNormalPhone($request->get('phone')))
            ->wherePassword($request->get('password'))
            ->firstOrFail();

        return $this->response('Успешная авторизация', new UserResource($user));
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
     * @param ValidatePhoneRequest $request
     * @param ValidateAndSendCodeChangeHandler $handler
     * @return JsonResponse
     */
    public function validatePhoneChange(ValidatePhoneRequest $request, ValidateAndSendCodeChangeHandler $handler): JsonResponse
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->get('user');
        return $this->response('Профиль', new UserResource($user));
    }

    /**
     * @param EditProfileRequest $request
     * @param EditProfileHandler $handler
     * @return JsonResponse
     */
    public function editProfile(EditProfileRequest $request, EditProfileHandler $handler): JsonResponse
    {
        $user = $handler->handle($request->get('user'), $request->validated());
        return $this->response('Успешно изменен', new UserResource($user));
    }
}
