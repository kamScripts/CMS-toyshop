<?php

class Database {


    public function __construct
    (
        private string $host,
        private string $db,
        private string $user,
        private string $password,
        private string $chrset
    )
    {}
    public function connect(): PDO
    {
        $dbAttributes = "mysql:host=$this->host;dbname=$this->db;charset=$this->chrset";
        $options = [
            // Throw exceptions on database errors instead of silent failures;
            // essential for debugging and consistent error handling.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Return rows as associative arrays only (no numeric indexes),
            // making results cleaner and easier to work with.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // prepared statements provided by MySQL;
            // improves security (prevents SQL injection) and ensures correct data typing.
            PDO::ATTR_EMULATE_PREPARES => false,
            // Prevent PDO from converting numeric values into strings;
            // keeps integers and decimals in their proper native PHP types.
            PDO::ATTR_STRINGIFY_FETCHES => false
        ];

        try {
            return new PDO($dbAttributes, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            // In production log instead
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }
}
