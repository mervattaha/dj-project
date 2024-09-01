<?php
namespace App\Controllers;

use App\Controllers\MorePlacesController; // تأكد من تضمين MorePlacesController

class BaseController {
    protected $twig;
    protected $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }
}
