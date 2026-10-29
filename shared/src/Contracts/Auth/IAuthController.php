<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Auth;

interface IAuthController
{
    public function handleHealth(array $request): array;
    public function handleSessionCheck(array $request): array;
    public function handleLogin(array $request): array;
    public function handleRegister(array $request): array;
    public function handleLogout(array $request): array;
    public function handleSetGender(array $request): array;
    public function handleForgotPassword(array $request): array;
    public function handleResetPassword(array $request): array;
    public function handleValidateKey(array $request): array;
}
