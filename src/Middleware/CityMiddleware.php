<?php

namespace App\Middleware;

class CityMiddleware {
    private $morePlacesController;
    private $userLocation;

    public function __construct($morePlacesController, $userLocation) {
        $this->morePlacesController = $morePlacesController;
        $this->userLocation = $userLocation;
    }

    public function __invoke() {
        // Fetch nearby cities
        $nearbyCities = $this->morePlacesController->getNearbyCities($this->userLocation);

        // Store in global variables for Twig
        global $twig;
        $twig->addGlobal('nearbyCities', $nearbyCities);
    }
}
