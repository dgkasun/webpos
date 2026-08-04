<?php

/**
 * Handles user-related database operations.
 * The database connection is provided through constructor injection,
 * Ref: Fowler, M. (2004) - https://martinfowler.com/articles/injection.html
 */
class User
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findActiveByUsername(string $username)
    {
        $userQuery = $this->conn->prepare(
            'SELECT id, name, username, password, role
             FROM users
             WHERE username = :username AND is_active = 1 LIMIT 1'
        );
        $userQuery->execute([
            'username' => $username,
        ]);
        return $userQuery->fetch(PDO::FETCH_ASSOC);
    }
}
