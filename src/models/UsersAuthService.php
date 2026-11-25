<?php

namespace src\models;

class UsersAuthService
{
    public static function createToken(User $user)
    {
        $token = $user->getId() . ':' . $user->getAuthToken();
        setcookie('token', $token, 0, '/', '', false, true);
    }
    public static function getUserByToken(): ?User
    {
        $token = $_COOKIE['token'] ?? '';
        if (empty($token)) {
            return null;
        }
        [$userId, $authToken] = explode(':', $token, 2);

        $user = User::getById((int) $userId);

        if( $user === null) {
            return null;
        }

        if($user->getAuthToken() !== $authToken){
            return null;
        }

        return $user;
    }

    public static function getUserByAuthBasic(): ?User
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

        if (empty($authHeader) && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (empty($authHeader) || stripos($authHeader, 'Basic ') !== 0) {
            return null;
        }

        $encodedCredentials = trim(substr($authHeader, 6));
        $decodedCredentials = base64_decode($encodedCredentials, true);

        $credentials = ($decodedCredentials !== false && strpos($decodedCredentials, ':') !== false)
            ? $decodedCredentials
            : $encodedCredentials;

        if (strpos($credentials, ':') === false) {
            return null;
        }

        [$nickname, $password] = explode(':', $credentials, 2);

        $user = User::findOneByColumn('nickname', $nickname);
        if ($user === null) {
            return null;
        }

        if (!password_verify($password, $user->getPasswordHash())) {
            return null;
        }

        return $user;
    }

    public static function getUserByApiToken(): ?User
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

        if (empty($authHeader) && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (empty($authHeader) || stripos($authHeader, 'Bearer ') !== 0) {
            return null;
        }

        $token = trim(substr($authHeader, 7));

        if (empty($token)) {
            return null;
        }

        return User::findOneByColumn('auth_token', $token);
    }
}
