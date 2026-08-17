<?php

/**
 * Handles user data and database operations.
 * The database connection is passed through the constructor.
 * 
 * Ref: Fowler, M. (2004)
 */
class User
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Find an active user by username
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
