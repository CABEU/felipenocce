<?php
declare(strict_types=1);

/**
 * Conexão única (singleton) com o banco atendelab via PDO.
 * Ajuste host/usuário/senha conforme o ambiente (XAMPP local por padrão).
 */
function conexaoBanco(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host   = '127.0.0.1';
        $dbname = 'atendelab';
        $usuario = 'root';
        $senha   = '';
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $usuario, $senha, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(500);
            echo json_encode([
                'erro' => 'Não foi possível conectar ao banco atendelab. Verifique config/database.php e se o MySQL está ativo.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    return $pdo;
}
