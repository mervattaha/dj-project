<?php
// src/controllers/CityController.php

namespace src\controllers;

class CityController {
    private $pdo;
    private $twig;

    public function __construct($pdo, $twig) {
        $this->pdo = $pdo;
        $this->twig = $twig;
    }

    public function showDJsByCity($cityName) {
        // استعلام SQL للحصول على DJs بناءً على المدينة
        $sql = "
            SELECT d.*, c.latitude, c.longitude
            FROM djs d
            JOIN cities c ON d.city_name = c.city_name
            WHERE d.city_name = :cityName
        ";

        // تحضير وتنفيذ الاستعلام
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cityName' => $cityName]);
        $djs = $stmt->fetchAll();

        // عرض النتائج باستخدام Twig
        echo $this->twig->render('djs-by-city.twig', [
            'city_name' => $cityName,
            'djs' => $djs
        ]);
    }
}
