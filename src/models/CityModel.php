<?php
namespace App\Models;

use PDO;
use PDOException;

class CityModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    private function getNearbyCities($latitude, $longitude, $radius = 50) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT city_name, country_code, latitude, longitude,
                    (6371 * ACOS(
                        COS(RADIANS(:lat)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(:long)) +
                        SIN(RADIANS(:lat)) * SIN(RADIANS(latitude))
                    )) AS distance
                FROM cities
                HAVING distance < :radius
                ORDER BY distance
                LIMIT 10
            ");
            $stmt->execute([
                ':lat' => $latitude,
                ':long' => $longitude,
                ':radius' => $radius
            ]);
    
            $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $cities;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
    
}