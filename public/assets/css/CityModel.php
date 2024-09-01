<?php
namespace App\Models;


class CityModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getNearbyCities($latitude, $longitude) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT city_name, city_slug,
                ( (latitude - :lat) * (latitude - :lat) + (longitude - :long) * (longitude - :long) ) AS distance
                FROM cities
                ORDER BY distance
                LIMIT 10
            ");
            $stmt->execute([
                ':lat' => $latitude,
                ':long' => $longitude
            ]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return [];
        }
    }
}
