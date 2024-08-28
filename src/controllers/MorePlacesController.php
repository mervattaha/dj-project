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
            // الحصول على موقع المستخدم
            $userLocation = $this->getUserLocation();

            // جلب المدن القريبة
            $cities = $this->getNearbyCities($userLocation);

            // جلب المواقع المميزة
            $topLocations = $this->getTopLocations();

            // جلب الدول
            $countries = $this->getCountries();

            // عرض القالب مع المدن القريبة، المواقع المميزة، والدول
            $this->renderWithFooter('more-places.twig', [
                'topLocations' => $topLocations,
                'cities' => $cities,
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

    public function getNearbyCities($userLocation)
    {
        $latitude = $userLocation['latitude'];
        $longitude = $userLocation['longitude'];
    
        try {
            $stmt = $this->pdo->prepare("
                SELECT city_name, country_code, latitude, longitude,
                (6371 * ACOS(
                    COS(RADIANS(:lat)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(:long)) +
                    SIN(RADIANS(:lat)) * SIN(RADIANS(latitude))
                )) AS distance
                FROM cities
                HAVING distance < 50
                ORDER BY distance
                LIMIT 10
            ");
            $stmt->execute([
                ':lat' => $latitude,
                ':long' => $longitude
            ]);
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        } catch (PDOException $e) {
            // سجل الخطأ بدلاً من عرضه
            error_log("Database error: " . $e->getMessage());
            return [];
        }
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
