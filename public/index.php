<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/utilities.php';
require_once __DIR__ . '/../src/LocationHelper.php';

use Bramus\Router\Router;
use App\Controllers\BookController;
use App\Controllers\CategoryController;
use App\Controllers\ContactController;
use App\Controllers\MorePlacesController;
use App\Controllers\DJController;
use App\Controllers\TraditionalWeddingsController;


// التأكد من تحميل ملفات الموديلات والمراقبين مرة واحدة فقط
require_once '../src/models/Booking.php';
require_once '../src/setup.php'; // تأكد من أنه لا يحتوي على تعريف دالة مكررة

// إعداد قاعدة البيانات
try {
    $pdo = new PDO('sqlite:../src/database/cueup.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// تحميل الترجمة بناءً على اللغة المحددة
$language = $_GET['lang'] ?? 'en'; // افتراض اللغة الإنجليزية إذا لم يتم تحديدها
$translations = loadTranslations($language);

// إعداد Twig
$loader = new \Twig\Loader\FilesystemLoader('../src/views'); // تأكد من أن هذا هو المسار الصحيح إلى القوالب
$twig = new \Twig\Environment($loader, [
    'cache' => false, // تعطيل الكاش
    'debug' => true,  // تفعيل وضع التصحيح إذا لزم الأمر
]);
$twig->addGlobal('translations', $translations);

// code that gets the lat and lon

// الحصول على قائمة المدن
$cities = LocationHelper::getNearbyCities($pdo,['latitude' => 30.0444, 'longitude' => 31.2357]);
$twig->addGlobal('cities', $cities);

// إعداد الروتر باستخدام Bramus Router
$router = new Router();

// المسار للصفحة الرئيسية
$router->get('/', function() use ($twig, $pdo) {
    try {
        // استرجع DJs المميزين من الجدول المنفصل
        $statement = $pdo->query('
            SELECT d.* 
            FROM djs d
            JOIN featured_djs f ON d.id = f.dj_id
        ');
        if (!$statement) {
            throw new Exception("Query failed");
        }
        $djs = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo $twig->render('home.twig', ['djs' => $djs]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

// المسار لصفحة قائمة DJs
$router->get('/djs', function() use ($twig, $pdo) {
    try {
        $statement = $pdo->query('SELECT * FROM djs');
        if (!$statement) {
            throw new Exception("Query failed");
        }
        $djs = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo $twig->render('djs.twig', ['djs' => $djs]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

// المسار لصفحة ملف تعريف DJ معين
$router->get('/dj', function() use ($twig, $pdo) {
    if (isset($_GET['id'])) {
        try {
            $statement = $pdo->prepare('SELECT * FROM djs WHERE id = :id');
            $statement->execute(['id' => $_GET['id']]);
            $dj = $statement->fetch(PDO::FETCH_ASSOC);

            if ($dj) {
                echo $twig->render('dj_profile.twig', ['dj' => $dj]);
            } else {
                echo $twig->render('404.twig', ['message' => 'DJ not found']);
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo $twig->render('404.twig', ['message' => 'No DJ ID provided!']);
    }
});

// المسار لنموذج الحجز
$router->get('/book', function() use ($twig, $pdo) {
    $bookController = new BookController($twig, $pdo);
    $bookController->showBookingForm();
});

// المسار لمعالجة الحجز
$router->post('/book-process', function() use ($twig, $pdo) {
    $bookController = new BookController($twig, $pdo);
    $bookController->processBooking();
});

// المسار للفئات والفئات الفرعية
$router->get('/event_categories', function() use ($twig, $pdo) {
    $categoryController = new CategoryController($twig, $pdo);
    $categoryController->showCategories();
});

$router->get('/event_categories/{categorySlug}/{subcategorySlug}', function($categorySlug, $subcategorySlug) use ($twig, $pdo) {
    $categoryController = new CategoryController($twig, $pdo);
    $categoryController->showSubcategory($categorySlug, $subcategorySlug);
});

$router->get('/event_categories/{categorySlug}', function($categorySlug) use ($twig, $pdo) {
    $categoryController = new CategoryController($twig, $pdo);
    $categoryController->showSubcategories($categorySlug);
});



// المسار لصفحة "اتصل بنا"
$router->get('/contact', function() use ($twig, $pdo, $language) {
    $translations = loadTranslations($language); // استخدم اللغة الحالية هنا
    echo $twig->render('contact.twig', ['translations' => $translations]);
});

// المسار لصفحة "معلومات عنا"
$router->get('/about', function() use ($twig, $pdo, $language) {
    $translations = loadTranslations($language); // استخدم اللغة الحالية هنا
    echo $twig->render('about.twig', ['translations' => $translations]);
});

// المسار لمعالجة نموذج الاتصال
$router->post('/contact-process', function() use ($twig, $pdo) {
    $contactController = new ContactController($pdo, $twig);
    $contactController->handleContactForm();
});

// المسار لصفحة "أماكن أكثر"
$router->get('/more-places', function() use ($twig, $pdo) {
    $controller = new MorePlacesController($twig, $pdo);
    $controller->showMorePlaces();
});

// المسار لعرض DJs بناءً على المدينة
$router->get('/djs/city/{city}', function($city) use ($twig, $pdo) {
    try {
        $statement = $pdo->prepare('SELECT * FROM djs WHERE city = :city');
        $statement->execute(['city' => $city]);
        $djs = $statement->fetchAll(PDO::FETCH_ASSOC);

        echo $twig->render('djs.twig', ['djs' => $djs, 'city' => $city]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});


$router->get('/traditional-weddings/{subcategorySlug}', function($subcategorySlug) use ($twig, $pdo) {
    $controller = new TraditionalWeddingsController($twig, $pdo);
    $controller->showSubcategory($subcategorySlug);
});



$router->get('/traditional-weddings', function() use ($twig, $pdo) {
    $controller = new TraditionalWeddingsController($twig, $pdo);
    $language = $_GET['lang'] ?? 'en'; // Default to 'en' if not specified
    $controller->showTraditionalWeddings($language);
});



// المسار لعرض DJs بناءً على الدولة
$router->get('/djs/country/{country}', function($country) use ($twig, $pdo) {
    try {
        // الحصول على المدن بناءً على الدولة
        $stmt = $pdo->prepare('SELECT city FROM cities WHERE country_code = :country');
        $stmt->execute(['country' => $country]);
        $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($cities)) {
            echo $twig->render('404.twig', ['message' => 'No cities found for this country.']);
            return;
        }

        // الحصول على DJs بناءً على المدن
        $placeholders = implode(',', array_fill(0, count($cities), '?'));
        $stmt = $pdo->prepare("SELECT * FROM djs WHERE city IN ($placeholders)");
        $stmt->execute($cities);
        $djs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo $twig->render('djs.twig', ['djs' => $djs, 'country' => $country]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

// تنفيذ التوجيه استنادًا إلى URI
$router->run();
