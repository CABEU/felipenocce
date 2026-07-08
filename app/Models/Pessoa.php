<?php

namespace App\Models;

use App\Core\Database;

class Pessoa
{
    public static function todos(): array
    {
        return Database::pdo()
            ->query("SELECT * FROM pessoas ORDER BY id ASC")
            ->fetchAll();
    }

    /** Somente pessoas ativas (para selects de atendimento). */
    public static function ativas(): array
    {
        return Database::pdo()
            ->query("SELECT * FROM pessoas WHERE ativo = 1 ORDER BY nome ASC")
            ->fetchAll();
    }

    public static function encontrar(int $id): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM pessoas WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function criar(array $d): void
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO pessoas (nome, documento, email, curso, periodo, ativo)
             VALUES (:nome, :documento, :email, :curso, :periodo, :ativo)"
        );
        $stmt->execute([
            ':nome'      => $d['nome'],
            ':documento' => $d['documento'],
            ':email'     => $d['email'],
            ':curso'     => $d['curso'],
            ':periodo'   => $d['periodo'],
            ':ativo'     => $d['ativo'] ?? 1,
        ]);
    }

    public static function atualizar(int $id, array $d): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE pessoas
             SET nome = :nome, documento = :documento, email = :email,
                 curso = :curso, periodo = :periodo
             WHERE id = :id"
        );
        $stmt->execute([
            ':nome'      => $d['nome'],
            ':documento' => $d['documento'],
            ':email'     => $d['email'],
            ':curso'     => $d['curso'],
            ':periodo'   => $d['periodo'],
            ':id'        => $id,
        ]);
    }

    /** Alterna entre ativo e inativo, preservando o histórico. */
    public static function alternarStatus(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE pessoas SET ativo = 1 - ativo WHERE id = ?"
        );
        $stmt->execute([$id]);
    }

    public static function total(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM pessoas")->fetchColumn();
    }
}
