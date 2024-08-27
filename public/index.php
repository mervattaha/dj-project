<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../includes/Router.php'; // تأكد من صحة المسار
require '../vendor/autoload.php';

// التأكد من تحميل ملفات الموديلات والمراقبين مرة واحدة فقط
require_once '../src/models/Booking.php';
require_once '../src/controllers/BookController.php';
require_once '../src/controllers/DJController.php';
require_once '../src/controllers/CategoryController.php';
require_once '../src/utilities.php';
require_once '../src/setup.php'; // تأكد من أنه لا يحتوي على تعريف دالة مكررة
require_once '../src/controllers/ContactController.php';
require_once '../src/controllers/MorePlacesController.php';

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

// إعداد قاعدة البيانات
try {
    $pdo = new PDO('sqlite:../src/database/cueup.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// إعداد الروتر مع تمرير Twig و PDO
$router = new Router($twig, $pdo);

// المسار للصفحة الرئيسية
$router->add('/', function() use ($twig, $pdo) {
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
$router->add('/djs', function() use ($twig, $pdo) {
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
$router->add('/dj', function() use ($twig, $pdo) {
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
$router->add('/book', function() use ($twig, $pdo) {
    $bookController = new BookController($twig, $pdo);
    $bookController->showBookingForm();
});

// المسار لمعالجة الحجز
$router->add('/book-process', function() use ($twig, $pdo) {
    $bookController = new BookController($twig, $pdo);
    $bookController->processBooking();
});

// المسار للفئات والفئات الفرعية
$router->add('/event_categories', function() use ($twig, $pdo) {
    $categoryController = new CategoryController($twig, $pdo);
    $categoryController->showCategories();
});

$router->add('/event_categories/{categorySlug}', function($categorySlug) use ($twig, $pdo) {
    $categoryController = new CategoryController($twig, $pdo);
    $categoryController->showSubcategories($categorySlug);
});

$router->add('/event_categories/{categorySlug}/{subcategorySlug}', function($categorySlug, $subcategorySlug) use ($twig, $pdo) {
    $categoryController = new CategoryController($twig, $pdo);
    $categoryController->showSubcategory($categorySlug, $subcategorySlug);
});

// المسار لصفحة "اتصل بنا"
$router->add('/contact', function() use ($twig, $pdo, $language) {
    $translations = loadTranslations($language); // استخدم اللغة الحالية هنا
    echo $twig->render('contact.twig', ['translations' => $translations]);
});

// المسار لصفحة "معلومات عنا"
$router->add('/about', function() use ($twig, $pdo, $language) {
    $translations = loadTranslations($language); // استخدم اللغة الحالية هنا
    echo $twig->render('about.twig', ['translations' => $translations]);
});

// المسار لمعالجة نموذج الاتصال
$router->add('/contact-process', function() use ($twig, $pdo) {
    $contactController = new ContactController($pdo, $twig);
    $contactController->handleContactForm();
});

// المسار لصفحة "أماكن أكثر"
$router->add('/more-places', function() use ($twig, $pdo) {
    $controller = new MorePlacesController($twig, $pdo);
    $controller->showMorePlaces();
});
// المسار لعرض DJs بناءً على المدينة
$router->add('/djs/city/{city}', function($city) use ($twig, $pdo) {
    try {
        $statement = $pdo->prepare('SELECT * FROM djs WHERE city = :city');
        $statement->execute(['city' => $city]);
        $djs = $statement->fetchAll(PDO::FETCH_ASSOC);

        echo $twig->render('djs.twig', ['djs' => $djs, 'city' => $city]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

// المسار لعرض DJs بناءً على الدولة
$router->add('/djs/country/{country}', function($country) use ($twig, $pdo) {
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
$router->dispatch(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
