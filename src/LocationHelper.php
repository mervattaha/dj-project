<?php

class LocationHelper {
    public static function getNearbyCities($pdo, $userLocation) {
        $latitude = $userLocation['latitude'];
        $longitude = $userLocation['longitude'];

        try {
            $stmt = $pdo->prepare("
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

            $uniqueCities = [];
            foreach ($cities as $city) {
                $cityKey = $city['city_name'] . $city['country_code'];
                if (!isset($uniqueCities[$cityKey])) {
                    $uniqueCities[$cityKey] = $city;
                }
            }

            return array_values($uniqueCities);
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return [];
        }
    }
}
