<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\Intl\IntlExtension; // تأكد من أنك تستخدم الفئة الصحيحة

// تحميل الترجمة بناءً على اللغة المحددة
$language = $_GET['lang'] ?? 'en'; // افتراض اللغة الإنجليزية إذا لم يتم تحديدها
$translations = loadTranslations($language);

// إعداد Twig
$loader = new FilesystemLoader(__DIR__ . '/../src/views');
$twig = new Environment($loader, [
    'cache' => false, // تعطيل التخزين المؤقت
    'debug' => true,  // تفعيل وضع التصحيح
]);

// إضافة امتداد الترجمة
$twig->addExtension(new IntlExtension());

// إضافة فلتر الترجمة
$twig->addFilter(new \Twig\TwigFilter('trans', function ($string) use ($translations) {
    return $translations[$string] ?? $string;
}));

return $twig;
