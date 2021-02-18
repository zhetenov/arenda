<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\InCorrectTokenException;
use App\Exceptions\NotAuthorizedException;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws InCorrectTokenException
     * @throws NotAuthorizedException
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('token');
        if (empty($token)) {
            throw new NotAuthorizedException('Вы не авторизованы', 401);
        }

        $user = User::whereToken($token)->first();
        if (!$user) {
            throw new InCorrectTokenException('Не правильный токен', 404);
        }

        $request->attributes->add(['user' => $user]);

        return $next($request);
    }
}
