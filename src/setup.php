<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\Intl\IntlExtension;
use App\Repositories\DJRepository;

// Set up Twig
$loader = new FilesystemLoader(__DIR__ . '/../src/views');
$twig = new Environment($loader, [
    'cache' => false, // Set to true for production
    'debug' => true,
]);

// Add the IntlExtension
$twig->addExtension(new IntlExtension());

// Add global variables to Twig
$twig->addGlobal('image_path', '/images/');

// Setup database connection
try {
    $pdo = new PDO('sqlite:../src/database/cueup.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize DJRepository
$djRepository = new DJRepository($pdo);

// Make sure to include other necessary initializations or configurations
