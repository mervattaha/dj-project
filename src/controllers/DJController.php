<?php

namespace App\Controllers;

use App\Repositories\DJRepository;
use Twig\Environment;
use PDO;

class DJController extends BaseController
{
    protected $twig;
    protected $djRepository;
    protected  $pdo;

    public function __construct(Environment $twig, DJRepository $djRepository, PDO $pdo)
    {
        $this->twig = $twig;
        $this->djRepository = $djRepository;
        $this->pdo = $pdo;
    }

    public function searchDJs($query): array
    {
        try {
            return $this->djRepository->search($query);
        } catch (\Exception $e) {
            $this->handleError($e);
            return [];
        }
    }
    

    public function showDJProfile(int $id): void
    {
        try {
            $dj = $this->djRepository->getDJById($id);

            if ($dj) {
                $countryName = $this->getCountryNameForDJ($dj['city']);
                echo $this->twig->render('dj_profile.twig', [
                    'dj' => $dj,
                    'country_name' => $countryName
                ]);
            } else {
                echo $this->twig->render('404.twig', ['message' => 'DJ not found']);
            }
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    private function getCountryNameForDJ($city)
    {
        // Implement logic to get country name based on city
        $stmt = $this->pdo->prepare('
            SELECT co.country_name
            FROM cities ci
            JOIN countries co ON ci.country_code = co.country_code
            WHERE ci.city_name = :city
        ');
        $stmt->execute(['city' => $city]);
        return $stmt->fetchColumn();
    }

    private function handleError(\Exception $e)
    {
        // Log the error and render an error page
        error_log($e->getMessage(), 3, __DIR__ . '/../logs/error.log');
        echo $this->twig->render('error.twig', ['message' => 'An error occurred. Please try again later.']);
    }
}