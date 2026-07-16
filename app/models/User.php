<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

class User extends BaseModel
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, username, password, full_name, email, role, status, first_login
             FROM users
             WHERE username = :username AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['username' => $username]);

        $user = $stmt->fetch();

        return $user === false ? null : $user;
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
