<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use PDO;
use PDOException;
use Exception;

class MorePlacesController extends BaseController
{
    public function showMorePlaces()
    {
        try {
            // جلب المواقع المميزة
            $topLocations = $this->getTopLocations();

            // جلب الدول
            $countries = $this->getCountries();

            // عرض القالب مع المدن القريبة، المواقع المميزة، والدول
            echo $this->twig->render('more-places.twig', [
                'topLocations' => $topLocations,
                'countries' => $countries
            ]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getUserLocation()
    {
        // موقع ثابت كمثال؛ استبدله بمنطق الموقع الفعلي
        return ['latitude' => 30.0444, 'longitude' => 31.2357]; // القاهرة، على سبيل المثال
    }

    

    private function getTopLocations()
    {
        try {
            $stmt = $this->pdo->query("SELECT city_name, city_slug FROM top_locations");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return [];
        }
    }

    private function getCountries()
    {
        try {
            $stmt = $this->pdo->query("SELECT country_name, country_code FROM countries");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return [];
        }
    }
}
