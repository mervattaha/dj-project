<?php
use App\Repositories\DJRepository;

// Display errors for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/container.php'; // Ensure this file sets up $container
require_once __DIR__ . '/../src/models/CityModel.php';
require_once __DIR__ . '/../src/LocationHelper.php';
require_once __DIR__ . '/../src/models/BookingModel.php';
require_once __DIR__ . '/../src/setup.php'; // Ensure it doesn't contain duplicate function definitions
require_once __DIR__ . '/../src/utilities.php';  // Ensure this is included before using loadTranslations()


use Bramus\Router\Router;
use Pimple\Container;
use App\Controllers\BookController;
use App\Controllers\CategoryController;
use App\Controllers\ContactController;
use App\Controllers\MorePlacesController;
use App\Controllers\DJController;
use App\Models\BookingModel;
use Twig\Loader\FilesystemLoader;
use App\Models\CityModel;
use Twig\Environment;
use PDO;

session_start();

// Load translations
$locale = $_GET['lang'] ?? 'en'; // Default to English
$translations = loadTranslations($locale);


// Initialize Twig
$loader = new FilesystemLoader(__DIR__ . '/../src/views');
$twig = new Environment($loader, [
    'cache' => false, // Set to true in production and provide a cache directory
    'debug' => true, // Set to false in production
]);

// Correct path to the SQLite database file
$pdo = new PDO('sqlite:' . __DIR__ . '/../src/database/cueup.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create the router
$router = new Router();
// Initialize controllers with translations

// Example route for more places
if ($_SERVER['REQUEST_URI'] === '/more-places') {
    $morePlacesController->showMorePlaces();
}


$router->get('/event_categories/more-places', function() use ($twig, $pdo, $translations) {
    $controller = new MorePlacesController($twig, $pdo, $translations);
    $controller->showMorePlaces();
});


// Define routes

// Home page
$router->get('/', function() use ($twig, $pdo) {
    try {
        // Fetch featured DJs
        $statement = $pdo->query('
            SELECT d.* 
            FROM djs d
            JOIN featured_djs f ON d.id = f.dj_id
        ');
        $djs = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo $twig->render('home.twig', ['djs' => $djs]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

// DJs list page
$router->get('/djs', function() use ($twig, $pdo) {
    try {
        $statement = $pdo->query('SELECT * FROM djs');
        $djs = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo $twig->render('djs.twig', ['djs' => $djs]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

// DJ profile page
$router->get('/dj', function() use ($twig, $pdo) {
    if (isset($_GET['id'])) {
        try {
            $statement = $pdo->prepare('SELECT * FROM djs WHERE id = :id');
            $statement->execute(['id' => $_GET['id']]);
            $dj = $statement->fetch(PDO::FETCH_ASSOC);

            if ($dj) {
                // Assuming you have a CityModel to fetch country information
                $city = new CityModel($pdo);
                $country_name = $city->getCountry($dj['city_id']);
                echo $twig->render('dj_profile.twig', ['dj' => $dj, 'country_name' => $country_name]);
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

// Assuming BookingModel is properly included and initialized

// Initialize the BookingModel
$bookingModel = new BookingModel($pdo); // Adjust as needed based on the BookingModel constructor

// Book form
$router->get('/book', function() use ($twig, $pdo, $bookingModel) {
    $bookController = new BookController($twig, $pdo, $bookingModel);
    $bookController->showBookingForm();
});

// Process booking
$router->post('/book-process', function() use ($twig, $pdo, $bookingModel) {
    $bookController = new BookController($twig, $pdo, $bookingModel);
    $bookController->processBooking();
});

// Event categories
$router->get('/event_categories', function() use ($twig, $pdo) {
    $controller = new CategoryController($twig, $pdo);
    $controller->showCategories();
});

$router->get('/event_categories/{categorySlug}/{subcategorySlug}', function($categorySlug, $subcategorySlug) use ($twig, $pdo) {
    $categoryController = new CategoryController($twig, $pdo);
    $categoryController->showSubcategory($categorySlug, $subcategorySlug);
});

$router->get('/event_categories/{categorySlug}', function($categorySlug) use ($twig, $pdo) {
    $categoryController = new CategoryController($twig, $pdo);
    $categoryController->showSubcategories($categorySlug);
});
$language = $_GET['lang'] ?? 'en'; // Default to 'en' if not provided
$translations = loadTranslations($language);
$twig->addGlobal('translations', $translations);

// About page
$router->get('/about', function() use ($twig, $pdo, $language) {
    $translations = loadTranslations($language);
    echo $twig->render('about.twig', ['translations' => $translations]);
});

// Route to show the contact form
$router->get('/contact', function() use ($twig, $pdo) {
    $contactController = new ContactController($pdo, $twig);
    $contactController->showContactForm();
});

$router->post('/contact-process', function() use ($twig, $pdo) {
    $contactController = new ContactController($pdo, $twig);
    $contactController->handleContactForm();
});
// Route to show the success message
$router->get('/contact-success', function() use ($twig) {
    echo $twig->render('contact_success.twig');
});





// DJs by city
$router->get('/djs/city/{city}', function($city) use ($twig, $pdo) {
    try {
        $statement = $pdo->prepare('SELECT * FROM djs WHERE city = :city');
        $statement->execute(['city' => $city]);
        $djs = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo $twig->render('djs.twig', ['djs' => $djs, 'city' => $city, 'country' => null]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

// DJs by country
$router->get('/djs/country/{country}', function($country) use ($twig, $pdo) {
    try {
        $stmt = $pdo->prepare('SELECT city FROM cities WHERE country_code = :country');
        $stmt->execute(['country' => $country]);
        $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($cities)) {
            echo $twig->render('404.twig', ['message' => 'No cities found for this country.']);
            return;
        }

        $placeholders = implode(',', array_fill(0, count($cities), '?'));
        $stmt = $pdo->prepare("SELECT * FROM djs WHERE city IN ($placeholders)");
        $stmt->execute($cities);
        $djs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo $twig->render('djs.twig', ['djs' => $djs, 'city' => null, 'country' => $country]);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

// Country page
$router->get('/country/{countryCode}', function($countryCode) use ($twig, $pdo) {
    $controller = new \src\controllers\CountryController($pdo, $twig);
    $controller->showCountry($countryCode);
});

// DJs by country name
$router->get('/djs/country/{countryName}', function($countryName) use ($twig, $pdo) {
    $controller = new \src\controllers\CountryController($pdo, $twig);
    $controller->showDJsByCountry($countryName);
});

// Search DJs
$router->get('/search-djs', function() use ($twig, $pdo) {
    $djRepository = new DJRepository($pdo);
    $djController = new DJController($twig, $djRepository, $pdo);

    $query = $_GET['query'] ?? '';
    $djs = $djController->searchDJs($query);

    echo $twig->render('djs.twig', ['djs' => $djs, 'search_query' => $query, 'country_name' => $query]);
});
// Handle 404 Not Found
$router->set404(function() use ($twig) {
    echo $twig->render('404.twig');
});
// Run the router
$router->run();
