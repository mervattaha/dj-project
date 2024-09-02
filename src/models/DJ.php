<?php
namespace App\Models;

use PDO;

class DJ {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllDJs() {
        $stmt = $this->pdo->query('SELECT * FROM djs');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDJById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDJsByCity($city) {
        $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE city = :city');
        $stmt->execute([':city' => $city]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDJsByCountry($country) {
        // الحصول على المدن بناءً على الدولة
        $stmt = $this->pdo->prepare('SELECT city FROM cities WHERE country_code = :country');
        $stmt->execute([':country' => $country]);
        $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($cities)) {
            return []; // لا توجد مدن للدولة
        }

        // الحصول على DJs بناءً على المدن
        $placeholders = implode(',', array_fill(0, count($cities), '?'));
        $stmt = $this->pdo->prepare("SELECT * FROM djs WHERE city IN ($placeholders)");
        $stmt->execute($cities);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchDJs($query) {
        $sql = "SELECT * FROM djs WHERE name LIKE :query OR genre LIKE :query";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':query' => '%' . $query . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
