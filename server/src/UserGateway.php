<?php


class UserGateway
{
    private PDO $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->connect();
    }
    public function authenticate(string $email, string $password): array | false {}
    public function getUserByEmail(string $email): array | false {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUserByUsername(string $username): array | false {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUserById($userId): array | false {
        $sql = "SELECT * FROM users WHERE id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function createUser(string $username, string $email, string $password): string | false {
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    public function updateUserDetails(string $userId, string $username, string $email): int {
        $sql = "UPDATE users SET username = :username, email = :email WHERE id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        return $stmt->rowCount();
    }
    public function updateUserPassword(string $userId, string $newPassword): int {
        $sql = "UPDATE users SET password = :newPassword WHERE id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':newPassword', $newPassword);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        return $stmt->rowCount();
    }
}