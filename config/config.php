<?php
/**
 * Configuração central do AtendeLab.
 * Ajuste os dados de conexão conforme o seu ambiente (padrão XAMPP).
 */

return [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'atendelab',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name'      => 'AtendeLab',
        'subtitle'  => 'Controle de atendimentos acadêmicos',
        // base_url é detectada automaticamente, mas pode ser fixada aqui.
        'base_url'  => null,
    ],
    // Usuário administrador criado automaticamente na primeira execução.
    'seed_admin' => [
        'nome'  => 'Administrador',
        'login' => 'admin',
        'email' => 'admin@atendelab.com',
        'senha' => 'admin123',
    ],
];
