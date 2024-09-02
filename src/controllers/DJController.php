<?php
namespace App\Controllers;

use App\Repositories\DJRepository;
use Twig\Environment;
use PDO;

class DJController extends BaseController
{ 
    private $djRepository;
    protected $twig;
    protected  $pdo;

    public function __construct($djRepository, $twig, $pdo) {
        $this->djRepository = $djRepository;
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function showDJProfile($id) {
        // من المفترض أن تستخدم djRepository للحصول على معلومات الـ DJ
        $dj = $this->djRepository->getDJById($id);

        if ($dj) {
            echo $this->twig->render('dj_profile.twig', ['dj' => $dj]);
        } else {
            echo $this->twig->render('404.twig');
        }
    }
    
    public function searchDJs($query) {
        $query = '%' . $query . '%'; // Prepare the query string for SQL LIKE
        $sql = 'SELECT * FROM djs WHERE name LIKE :query OR genre LIKE :query';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':query' => $query]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    
    public function showAllDJs() {
        $djs = $this->djRepository->findAll();
        $this->renderWithFooter('home.twig', ['djs' => $djs]);
    }

    public function showDJsByCity($city) {
        $djs = $this->getDJsByCity($city);
        $this->renderWithFooter('djs.twig', [
            'djs' => $djs,
            'city' => $city
        ]);
    }

    public function showDJsByCountry($country) {
        $djs = $this->getDJsByCountry($country);
        $this->renderWithFooter('djs.twig', [
            'djs' => $djs,
            'city' => 'Various Cities in ' . $country
        ]);
    }

    private function getDJsByCity($city) {
        $sql = "SELECT * FROM djs WHERE city = :city";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':city' => $city]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getDJsByCountry($country) {
        $stmt = $this->pdo->prepare('SELECT city FROM cities WHERE country_code = :country');
        $stmt->execute([':country' => $country]);
        $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($cities)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($cities), '?'));
        $stmt = $this->pdo->prepare("SELECT * FROM djs WHERE city IN ($placeholders)");
        $stmt->execute($cities);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
