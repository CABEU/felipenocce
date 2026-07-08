<?php

namespace App\Models;

use App\Core\Database;

class Atendimento
{
    /** Lista com joins (nome da pessoa, tipo e responsável). */
    public static function todos(int $limite = 0): array
    {
        $sql = "SELECT a.*, p.nome AS pessoa_nome, t.nome AS tipo_nome, u.nome AS usuario_nome
                FROM atendimentos a
                JOIN pessoas p   ON p.id = a.pessoa_id
                JOIN tipos t     ON t.id = a.tipo_id
                JOIN usuarios u  ON u.id = a.usuario_id
                ORDER BY a.id DESC";
        if ($limite > 0) {
            $sql .= " LIMIT " . (int) $limite;
        }
        return Database::pdo()->query($sql)->fetchAll();
    }

    public static function encontrar(int $id): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM atendimentos WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function criar(array $d): void
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO atendimentos (pessoa_id, tipo_id, usuario_id, data, status, observacao)
             VALUES (:pessoa_id, :tipo_id, :usuario_id, :data, :status, :observacao)"
        );
        $stmt->execute([
            ':pessoa_id'  => $d['pessoa_id'],
            ':tipo_id'    => $d['tipo_id'],
            ':usuario_id' => $d['usuario_id'],
            ':data'       => $d['data'],
            ':status'     => $d['status'] ?? 'aberto',
            ':observacao' => $d['observacao'] ?? null,
        ]);
    }

    public static function definirStatus(int $id, string $status): void
    {
        $status = $status === 'concluido' ? 'concluido' : 'aberto';
        $stmt = Database::pdo()->prepare("UPDATE atendimentos SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    public static function total(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM atendimentos")->fetchColumn();
    }
}
