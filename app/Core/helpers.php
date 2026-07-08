<?php

/**
 * Funções auxiliares globais.
 */

if (!function_exists('e')) {
    /** Escapa saída HTML. */
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('base_url')) {
    /** URL base da aplicação (pasta public). */
    function base_url(): string
    {
        static $base = null;
        if ($base !== null) {
            return $base;
        }
        $cfg = $GLOBALS['config']['app']['base_url'] ?? null;
        if ($cfg) {
            return $base = rtrim($cfg, '/') . '/';
        }
        $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $script = rtrim($script, '/');
        return $base = $script . '/';
    }
}

if (!function_exists('url')) {
    /** Gera uma URL controller/action no padrão do AtendeLab. */
    function url(string $controller, string $action = 'index', array $params = []): string
    {
        $query = array_merge(['controller' => $controller, 'action' => $action], $params);
        return base_url() . '?' . http_build_query($query);
    }
}

if (!function_exists('old')) {
    /** Recupera valor anterior de formulário (após erro de validação). */
    function old(string $key, $default = ''): string
    {
        return e($_SESSION['old'][$key] ?? $default);
    }
}
