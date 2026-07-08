<?php

namespace App\Core;

/**
 * Controle de sessão / autenticação.
 */
class Auth
{
    public static function login(array $usuario): void
    {
        $_SESSION['usuario'] = [
            'id'    => (int) $usuario['id'],
            'nome'  => $usuario['nome'],
            'login' => $usuario['login'],
            'email' => $usuario['email'],
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['usuario']);
    }

    public static function check(): bool
    {
        return isset($_SESSION['usuario']);
    }

    public static function user(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['usuario']['id'] ?? null;
    }

    /** Redireciona para o login se não houver sessão ativa. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . url('auth', 'login'));
            exit;
        }
    }
}
