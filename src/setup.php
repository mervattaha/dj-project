<?php
require_once '../vendor/autoload.php';

require_once __DIR__ . '/utilities.php';

// إعداد Twig
$loader = new \Twig\Loader\FilesystemLoader('../src/views'); // المسار إلى دليل القوالب
$twig = new \Twig\Environment($loader);

// إعداد قاعدة بيانات
try {
    $pdo = new PDO('sqlite:../src/database/cueup.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// باقي إعدادات المشروع
