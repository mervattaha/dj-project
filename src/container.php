<?php

require_once '../vendor/autoload.php';
require_once '../src/container.php'; // Adjust path as needed

use Pimple\Container;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Repositories\DJRepository;
use App\Controllers\DJController;
use App\Controllers\ContactController;
use Bramus\Router\Router;

// Create the container
$container = new Container();

// Define services
$container['pdo'] = function() {
    return new PDO('sqlite:../path_to_your_database.db'); // Update the path to your database
};

$container['twig'] = function($c) {
    $loader = new FilesystemLoader('../src/views');
    return new Environment($loader);
};

$container['djRepository'] = function($c) {
    return new DJRepository($c['pdo']);
};

$container['djController'] = function($c) {
    return new DJController($c['twig'], $c['djRepository'], $c['pdo']);
};

$container['contactController'] = function($c) {
    return new ContactController($c['pdo'], $c['twig']);
};

// Create the router
$router = new Router();

// Define routes
$router->get('/dj/{id}', function($id) use ($container) {
    $controller = $container['djController'];
    $controller->showDJProfile((int)$id);
});

$router->get('/contact', function() use ($container) {
    $contactController = $container['contactController'];
    $contactController->showContactForm();
});

$router->post('/contact-process', function() use ($container) {
    $contactController = $container['contactController'];
    $contactController->handleContactForm();
});

// Run the router
$router->run();
