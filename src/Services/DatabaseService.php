<?php

namespace App\Services;

use PDO;
use PDOException;

class DatabaseService
{
    private readonly string $host;
    private readonly string $dbName;
    private readonly string $username;
    private readonly string $password;
    private readonly string $dsn;
    private PDO $pdo;
    public function __construct()
    {
        $this->host = $_ENV['DATABASE_HOST'];
        $this->dbName = $_ENV['DATABASE_NAME'];
        $this->username = $_ENV['DATABASE_USER'];
        $this->password = $_ENV['DATABASE_PASSWORD'];

        $this->dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException('Connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function query(string $query, array $params = []): false|array
    {
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $query, array $params = []): bool
    {
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute($params);
    }

    public function insert(string $query, array $params = []): int
    {
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute($params);
        return $this->getConnection()->lastInsertId();
    }
}