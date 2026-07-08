<?php

/**
 * Front controller do AtendeLab.
 * Roteamento no padrão: ?controller=<nome>&action=<acao>
 */

declare(strict_types=1);

use App\Core\Database;

session_start();

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Autoloader PSR-4 simples para o namespace App\
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = APP_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$config = require BASE_PATH . '/config/config.php';
$GLOBALS['config'] = $config;

require APP_PATH . '/Core/helpers.php';

// Inicializa banco + instalação automática do schema/seed.
Database::boot($config);

// Roteamento
$controllerName = $_GET['controller'] ?? 'auth';
$actionName     = $_GET['action'] ?? 'login';

$map = [
    'auth'     => \App\Controllers\AuthController::class,
    'frontend' => \App\Controllers\FrontendController::class,
];

if (!isset($map[$controllerName])) {
    http_response_code(404);
    exit('Controller não encontrado.');
}

$controller = new $map[$controllerName]();
$method = $actionName;

if (!method_exists($controller, $method)) {
    http_response_code(404);
    exit('Ação não encontrada.');
}

$controller->{$method}();
