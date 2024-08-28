<?php
namespace App\Controllers;

class DJController extends BaseController {
    private $djModel;

    public function __construct($twig, $pdo) {
        parent::__construct($twig, $pdo);
        $this->djModel = new DJ($pdo);
    }

    public function showAllDJs() {
        $djs = $this->djModel->getAllDJs();
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

    public function showDJ($id) {
        $dj = $this->djModel->getDJById($id);
        $this->renderWithFooter('dj_profile.twig', ['dj' => $dj]);
    }

    public function showDJProfile($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $dj = $stmt->fetch();

        if ($dj) {
            $this->renderWithFooter('dj_profile.twig', ['dj' => $dj]);
        } else {
            $this->renderWithFooter('404.twig', ['message' => 'DJ not found']);
        }
    }

    // باقي الدوال كما هي
}
