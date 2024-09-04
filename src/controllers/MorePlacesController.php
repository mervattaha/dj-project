<?php
namespace App\Controllers;

use PDO;

class MorePlacesController {
    private $twig;
    private $pdo;
    private $translations;

    public function __construct($twig, PDO $pdo, $translations) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->translations = $translations;
    }

    public function showMorePlaces() {
        try {
            // Get user location
            $userLocation = $this->getUserLocation();

            // Fetch nearby cities
            $cities = $this->getNearbyCities($userLocation);

            // Fetch top locations
            $topLocations = $this->getTopLocations(); 

            // Fetch countries
            $countries = $this->getCountries();

            // Render the view with nearby cities, top locations, and countries
            echo $this->twig->render('more-places.twig', [
                'topLocations' => $topLocations,
                'cities' => $cities,
                'countries' => $countries,
                'translations' => $this->translations // إضافة الترجمة إلى القالب
            ]);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private function getUserLocation() {
        // Example static location; replace with actual location logic
        return ['latitude' => 30.0444, 'longitude' => 31.2357]; // Cairo, for example
    }

    public function getNearbyCities($userLocation) {
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
    }

    private function getTopLocations() {
        try {
            $stmt = $this->pdo->query("SELECT city_name, city_slug FROM top_locations");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return [];
        }
    }

    private function getCountries() {
        try {
            $stmt = $this->pdo->query("SELECT country_name, country_code FROM countries");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return [];
        }
    }
}