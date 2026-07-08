<?php

namespace App\Models;

use App\Core\Database;

class Tipo
{
    public static function todos(): array
    {
        return Database::pdo()
            ->query("SELECT * FROM tipos ORDER BY id ASC")
            ->fetchAll();
    }

    public static function ativos(): array
    {
        return Database::pdo()
            ->query("SELECT * FROM tipos WHERE ativo = 1 ORDER BY nome ASC")
            ->fetchAll();
    }

    public static function encontrar(int $id): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM tipos WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function criar(array $d): void
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO tipos (nome, descricao, ativo) VALUES (:nome, :descricao, :ativo)"
        );
        $stmt->execute([
            ':nome'      => $d['nome'],
            ':descricao' => $d['descricao'],
            ':ativo'     => $d['ativo'] ?? 1,
        ]);
    }

    public static function atualizar(int $id, array $d): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE tipos SET nome = :nome, descricao = :descricao WHERE id = :id"
        );
        $stmt->execute([
            ':nome'      => $d['nome'],
            ':descricao' => $d['descricao'],
            ':id'        => $id,
        ]);
    }

    public static function alternarStatus(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE tipos SET ativo = 1 - ativo WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function total(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM tipos")->fetchColumn();
    }
}
