<?php

namespace App\Repositories;

use PDO;

class DJRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPdo()
    {
        return $this->pdo;
    }
    public function searchDJs($query) {
        // Example query; adjust based on your actual SQL and requirements
        $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE name LIKE ?');
        $stmt->execute(["%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // إضافة دوال للوصول إلى البيانات من قاعدة البيانات
    public function findById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

