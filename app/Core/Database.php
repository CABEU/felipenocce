<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Conexão PDO (singleton) + auto-instalação do schema e dados iniciais.
 */
class Database
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    public static function boot(array $config): void
    {
        self::$config = $config;
        self::ensureDatabase();
        self::pdo();
        self::install();
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $db = self::$config['db'];
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset={$db['charset']}";
            self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }

    /** Cria o banco caso ainda não exista. */
    private static function ensureDatabase(): void
    {
        $db = self::$config['db'];
        try {
            $dsn = "mysql:host={$db['host']};port={$db['port']};charset={$db['charset']}";
            $tmp = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $tmp->exec(
                "CREATE DATABASE IF NOT EXISTS `{$db['name']}`
                 CHARACTER SET {$db['charset']} COLLATE {$db['charset']}_unicode_ci"
            );
        } catch (PDOException $e) {
            self::die($e);
        }
    }

    /** Cria as tabelas (idempotente) e semeia os dados iniciais. */
    private static function install(): void
    {
        $pdo = self::pdo();

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(120) NOT NULL,
                login VARCHAR(60) NOT NULL,
                email VARCHAR(160) NOT NULL UNIQUE,
                senha_hash VARCHAR(255) NOT NULL,
                criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pessoas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(120) NOT NULL,
                documento VARCHAR(40) NOT NULL,
                email VARCHAR(160) NOT NULL,
                curso VARCHAR(120) NOT NULL,
                periodo VARCHAR(40) NOT NULL,
                ativo TINYINT(1) NOT NULL DEFAULT 1,
                criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS tipos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(120) NOT NULL,
                descricao VARCHAR(255) NOT NULL,
                ativo TINYINT(1) NOT NULL DEFAULT 1,
                criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS atendimentos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                pessoa_id INT NOT NULL,
                tipo_id INT NOT NULL,
                usuario_id INT NOT NULL,
                data DATE NOT NULL,
                status ENUM('aberto','concluido') NOT NULL DEFAULT 'aberto',
                observacao TEXT NULL,
                criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (pessoa_id) REFERENCES pessoas(id),
                FOREIGN KEY (tipo_id) REFERENCES tipos(id),
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        self::seed();
    }

    /** Popula os dados iniciais apenas se as tabelas estiverem vazias. */
    private static function seed(): void
    {
        $pdo = self::pdo();

        // Usuário administrador
        if ((int) $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn() === 0) {
            $admin = self::$config['seed_admin'];
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nome, login, email, senha_hash) VALUES (?,?,?,?)"
            );
            $stmt->execute([
                $admin['nome'],
                $admin['login'],
                $admin['email'],
                password_hash($admin['senha'], PASSWORD_DEFAULT),
            ]);
        }

        // Pessoas
        if ((int) $pdo->query("SELECT COUNT(*) FROM pessoas")->fetchColumn() === 0) {
            $pessoas = [
                ['Felipe Nocce Lucena', '52988177340', 'felipe.nocce.lucena@gmail.com', 'Ciência da Computação', '6º semestre', 1],
                ['Felipe Lucena', '48712095522', 'felipe.lucena@gmail.com', 'Análise e Desenvolvimento de Sistemas', '2º semestre', 0],
                ['Felipe Nocce', '30945612788', 'felipe.nocce@gmail.com', 'Engenharia da Computação', '8º semestre', 1],
                ['F. Nocce Lucena', '77123408866', 'f.noccelucena@gmail.com', 'Sistemas de Informação', '4º semestre', 0],
                ['Felipe N. Lucena', '61508329411', 'felipe.n.lucena@gmail.com', 'Ciência de Dados', '1º semestre', 0],
            ];
            $stmt = $pdo->prepare(
                "INSERT INTO pessoas (nome, documento, email, curso, periodo, ativo) VALUES (?,?,?,?,?,?)"
            );
            foreach ($pessoas as $p) {
                $stmt->execute($p);
            }
        }

        // Tipos
        if ((int) $pdo->query("SELECT COUNT(*) FROM tipos")->fetchColumn() === 0) {
            $tipos = [
                ['teste', 'teste 2', 0],
                ['orientação', 'teste', 1],
                ['Admistrativo', 'Atendimento relacionado a dúvidas administrativas, documentos e solicitações acadêmicas.', 1],
                ['Orientação acadêmica', 'Atendimento para orientação de atividades e projetos.', 0],
                ['Suporte técnico', 'Atendimento relacionado a problemas em laboratório ou sistemas.', 1],
                ['Monitoria', 'Atendimento para dúvidas de conteúdo acadêmico.', 1],
            ];
            $stmt = $pdo->prepare(
                "INSERT INTO tipos (nome, descricao, ativo) VALUES (?,?,?)"
            );
            foreach ($tipos as $t) {
                $stmt->execute($t);
            }
        }

        // Atendimentos
        if ((int) $pdo->query("SELECT COUNT(*) FROM atendimentos")->fetchColumn() === 0) {
            $usuarioId = (int) $pdo->query("SELECT id FROM usuarios ORDER BY id LIMIT 1")->fetchColumn();
            $pessoaNocceLucena = self::idPorNome('pessoas', 'Felipe Nocce Lucena');
            $pessoaLucena      = self::idPorNome('pessoas', 'Felipe Lucena');
            $tipoOrient  = self::idPorNome('tipos', 'orientação');
            $tipoSuporte = self::idPorNome('tipos', 'Suporte técnico');

            $stmt = $pdo->prepare(
                "INSERT INTO atendimentos (id, pessoa_id, tipo_id, usuario_id, data, status)
                 VALUES (?,?,?,?,?,?)"
            );
            $stmt->execute([2, $pessoaNocceLucena, $tipoSuporte, $usuarioId, '2026-05-30', 'aberto']);
            $stmt->execute([3, $pessoaLucena, $tipoOrient, $usuarioId, '2026-06-24', 'concluido']);
        }
    }

    private static function idPorNome(string $tabela, string $nome): int
    {
        $stmt = self::pdo()->prepare("SELECT id FROM {$tabela} WHERE nome = ? LIMIT 1");
        $stmt->execute([$nome]);
        return (int) $stmt->fetchColumn();
    }

    private static function die(PDOException $e): void
    {
        http_response_code(500);
        echo '<h1>Erro de conexão com o banco</h1>';
        echo '<p>Verifique se o MySQL do XAMPP está iniciado e os dados em <code>config/config.php</code>.</p>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        exit;
    }
}
