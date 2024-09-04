<?php
namespace App\Repositories;

use PDO;

class CountryRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCountryNameByCode($countryCode)
    {
        $stmt = $this->pdo->prepare('SELECT country_name FROM countries WHERE country_code = :code');
        $stmt->execute(['code' => $countryCode]);
        return $stmt->fetchColumn();
    }
}
