<?php
namespace src\controllers;

use PDO;
use Twig\Environment;

class CityController {
    private $pdo;
    private $twig;

    public function __construct(PDO $pdo, Environment $twig) {
        $this->pdo = $pdo;
        $this->twig = $twig;
    }

    public function getCountry($cityId) {
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

    public function getNearbyCities($latitude, $longitude) {
        $sql = "SELECT * FROM cities WHERE latitude BETWEEN ? AND ? AND longitude BETWEEN ? AND ?";
        $stmt = $this->pdo->prepare($sql);

        // Define range for latitude and longitude
        $latitudeRange = [$latitude - 0.1, $latitude + 0.1];
        $longitudeRange = [$longitude - 0.1, $longitude + 0.1];

        $stmt->execute(array_merge($latitudeRange, $longitudeRange));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showDJsByCity($cityName = null, $countryName = null) {
        $sql = "SELECT d.*, c.latitude, c.longitude FROM djs d JOIN cities c ON d.city_name = c.city_name WHERE d.city_name = :cityName";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cityName' => $cityName]);
        $djs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo $this->twig->render('djs_by_location.twig', [
            'city_name' => $cityName,
            'country_name' => $countryName,
            'djs' => $djs
        ]);
    }
}
