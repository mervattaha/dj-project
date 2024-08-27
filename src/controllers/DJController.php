<?php
require_once '../src/models/DJ.php';

class DJController {
    private $twig;
    private $pdo;
    private $djModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->djModel = new DJ($pdo);
    }

    public function getAllDJs() {
        $stmt = $this->pdo->query("SELECT id, name, description, image, city FROM djs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDJsByCity($city) {
        $stmt = $this->pdo->prepare("SELECT id, name, description, image, city FROM djs WHERE city = ?");
        $stmt->execute([$city]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDJsByCountry($country) {
        // الحصول على المدن بناءً على الدولة
        $stmt = $this->pdo->prepare("SELECT city FROM cities WHERE country_code = ?");
        $stmt->execute([$country]);
        $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($cities)) {
            // الحصول على DJs بناءً على المدن
            $placeholders = implode(',', array_fill(0, count($cities), '?'));
            $stmt = $this->pdo->prepare("SELECT id, name, description, image, city FROM djs WHERE city IN ($placeholders)");
            $stmt->execute($cities);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public function showAllDJs() {
        $djs = $this->djModel->getAllDJs();
        echo $this->twig->render('home.twig', ['djs' => $djs]);
    }

    public function showDJsByCity($city) {
        $djs = $this->getDJsByCity($city);
        echo $this->twig->render('djs.twig', ['djs' => $djs, 'city' => $city]);
    }

    public function showDJsByCountry($country) {
        $djs = $this->getDJsByCountry($country);
        echo $this->twig->render('djs.twig', ['djs' => $djs, 'city' => 'Various Cities in ' . $country]);
    }

    public function showDJ($id) {
        $dj = $this->djModel->getDJById($id);
        echo $this->twig->render('dj_profile.twig', ['dj' => $dj]);
    }

    public function showDJProfile($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $dj = $stmt->fetch();

        if ($dj) {
            echo $this->twig->render('dj_profile.twig', ['dj' => $dj]);
        } else {
            echo $this->twig->render('404.twig', ['message' => 'DJ not found']);
        }
    }

    public function showEventCategories() {
        $categories = [
            // الفئات كما هي
        ];

        echo $this->twig->render('event_categories.twig', ['categories' => $categories]);
    }

    public function showEventCategory($categorySlug) {
        // قائمة الفئات والأحداث الفرعية
        $categories = [
            // الفئات كما هي
        ];

        $category = strtolower($categorySlug);

        if (array_key_exists($category, $categories)) {
            echo $this->twig->render('event_subcategories.twig', ['categoryName' => ucfirst($category), 'categorySlug' => $category, 'subcategories' => $categories[$category]]);
        } else {
            echo $this->twig->render('404.twig', ['message' => 'Category not found']);
        }
    }

    public function showSubcategory($categorySlug, $subcategorySlug) {
        // قائمة الفئات والأحداث الفرعية
        $categories = [
            // الفئات كما هي
        ];

        $category = strtolower($categorySlug);
        $subcategory = str_replace('_', ' ', strtolower($subcategorySlug));

        if (array_key_exists($category, $categories) && in_array(ucfirst($subcategory), $categories[$category])) {
            echo $this->twig->render('subcategory.twig', ['subcategory' => ucfirst($subcategory)]);
        } else {
            echo $this->twig->render('404.twig', ['message' => 'Subcategory not found']);
        }
    }
}
