<?php
declare(strict_types=1);

/**
 * Front controller: inicia a sessão e delega tudo para routes.php,
 * que decide qual controller/action atender com base na URL.
 * Exemplo: /atendelab/public/?controller=auth&action=login
 */

session_start();

require __DIR__ . '/../routes.php';
