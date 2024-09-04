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
