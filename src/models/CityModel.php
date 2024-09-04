<?php

namespace App\Models;

use PDO;

class CityModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // دالة للحصول على الدول بناءً على معرف المدينة
    public function getCountry($cityId)
    {
        $stmt = $this->pdo->prepare('
            SELECT countries.name
            FROM cities
            JOIN countries ON cities.country_code = countries.code
            WHERE cities.id = :cityId
        ');
        $stmt->execute(['cityId' => $cityId]);
        $country = $stmt->fetch(PDO::FETCH_ASSOC);

        return $country ? $country['name'] : null;
    }

    // دالة للحصول على المدن القريبة بناءً على الإحداثيات
    public function getNearbyCities($latitude, $longitude)
    {
        // افترض أن الاستعلام يحتوي على عمود "name"
        $sql = "SELECT * FROM cities WHERE latitude BETWEEN ? AND ? AND longitude BETWEEN ? AND ?";
        $stmt = $this->pdo->prepare($sql);

        // قيم افتراضية لمثال. قد تحتاج لتعديل هذه القيم بناءً على احتياجاتك الفعلية
        $latitudeRange = [$latitude - 0.1, $latitude + 0.1];
        $longitudeRange = [$longitude - 0.1, $longitude + 0.1];

        $stmt->execute(array_merge($latitudeRange, $longitudeRange));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
