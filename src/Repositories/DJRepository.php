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

    public function searchDJsByLocation($location)
    {
        $stmt = $this->pdo->prepare('SELECT id FROM cities WHERE name LIKE ?');
        $stmt->execute(["%{$location}%"]);
        $city = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($city) {
            $cityId = $city['id'];
            $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE city_id = ?');
            $stmt->execute([$cityId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE city LIKE ?');
            $stmt->execute(["%{$location}%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function search($query)
    {
        $sql = "SELECT * FROM djs WHERE name LIKE :query";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['query' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDJById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
