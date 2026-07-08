<?php

namespace App\Models;

use App\Core\Database;

class Usuario
{
    public static function porEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
