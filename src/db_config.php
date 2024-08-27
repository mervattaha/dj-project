<?php
// db_config.php

$dsn = 'mysql:host=localhost;dbname=dj_database;charset=utf8';
$username = 'root'; // اسم المستخدم لقاعدة البيانات
$password = ''; // كلمة المرور لقاعدة البيانات

try {
    // إنشاء اتصال PDO
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // التعامل مع الأخطاء في حالة فشل الاتصال
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
