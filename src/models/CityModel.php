<?php

namespace App\Models;

use PDO;

class CityModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // دالة للحصول على الدول بناءً على معرف المدينة
    public function getCountry($cityId)
    {
        $stmt = $this->pdo->prepare('
            SELECT countries.country_name
            FROM cities
            JOIN countries ON cities.country_code = countries.country_code
            WHERE cities.id = :cityId
        ');
        $stmt->execute(['cityId' => $cityId]);
        $country = $stmt->fetch(PDO::FETCH_ASSOC);

        return $country ? $country['country_name'] : null;
    }

    // دالة للحصول على المدن القريبة بناءً على الإحداثيات
   /* public function getNearbyCities($userLocation) {
        $latitude = $userLocation['latitude'];
        $longitude = $userLocation['longitude'];

        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT city_name, country_code, latitude, longitude,
                ( (latitude - :lat) * (latitude - :lat) + (longitude - :long) * (longitude - :long) ) AS distance
                FROM cities
                ORDER BY distance
                LIMIT 10
            ");
            $stmt->execute([
                ':lat' => $latitude,
                ':long' => $longitude
            ]);

            $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Remove duplicates in PHP (if needed)
            $uniqueCities = [];
            foreach ($cities as $city) {
                $cityKey = $city['city_name'] . $city['country_code'];
                if (!isset($uniqueCities[$cityKey])) {
                    $uniqueCities[$cityKey] = $city;
                }
            }

            return array_values($uniqueCities);
        } catch (\PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return [];
        }
    }*/


}
