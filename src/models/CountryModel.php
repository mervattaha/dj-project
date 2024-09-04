<?php
namespace src\controllers;

use PDO;
use Twig\Environment;

class CountryController {
    private $pdo;
    private $twig;

    public function __construct(PDO $pdo, Environment $twig) {
        $this->pdo = $pdo;
        $this->twig = $twig;
    }

    public function showCountry($countryCode) {
        $stmt = $this->pdo->prepare('SELECT country_name FROM countries WHERE country_code = :code');
        $stmt->execute(['code' => $countryCode]);
        $countryName = $stmt->fetchColumn();

        echo $this->twig->render('country.twig', ['country_name' => $countryName]);
    }

    public function showDJsByCountry($countryName) {
        $sql = "
            SELECT d.*, co.country_name, co.latitude AS country_latitude, co.longitude AS country_longitude
            FROM djs d
            JOIN cities ci ON d.city_name = ci.city_name
            JOIN countries co ON ci.country_code = co.country_code
            WHERE co.country_name = :countryName
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['countryName' => $countryName]);
        $djs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo $this->twig->render('djs_by_location.twig', [
            'city_name' => null,
            'country_name' => $countryName,
            'djs' => $djs
        ]);
    }
}
