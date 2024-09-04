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
        try {
            // Correct column name 'country_code'
            $stmt = $this->pdo->prepare('SELECT country_name FROM countries WHERE country_code = :country_code');
            $stmt->execute(['country_code' => $countryCode]);
            $countryName = $stmt->fetchColumn();
    
            if ($countryName) {
                echo $this->twig->render('country.twig', ['country_name' => $countryName]);
            } else {
                echo $this->twig->render('404.twig', ['message' => 'Country not found.']);
            }
        } catch (\PDOException $e) {
            echo $this->twig->render('error.twig', ['message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
        } catch (\Exception $e) {
            echo $this->twig->render('error.twig', ['message' => 'An unexpected error occurred.']);
        }
    }
    
    

    public function showDJsByCountry($countryName) {
        try {
            $sql = "
                SELECT d.*, co.country_name, co.latitude AS country_latitude, co.longitude AS country_longitude
                FROM djs d
                JOIN cities ci ON d.city_name = ci.city_name
                JOIN countries co ON ci.country_code = co.country_code
                WHERE co.country_name = :country_name
            ";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['country_name' => $countryName]);
            $djs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            echo $this->twig->render('djs_by_location.twig', [
                'city_name' => null,
                'country_name' => $countryName,
                'djs' => $djs
            ]);
        } catch (\PDOException $e) {
            echo $this->twig->render('error.twig', ['message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
        } catch (\Exception $e) {
            echo $this->twig->render('error.twig', ['message' => 'An unexpected error occurred.']);
        }
    }
    
}
