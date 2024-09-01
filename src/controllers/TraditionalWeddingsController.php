<?php

namespace App\Controllers;

use PDO;

class TraditionalWeddingsController
{
    private $pdo;
    private $twig;

    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }
    public function showTraditionalWeddings($language = 'en')
    {
        $translations = $this->loadTranslations($language);
        
        $sql = "
            SELECT d.*, c.country_name
            FROM djs d
            LEFT JOIN countries c ON d.country_id = c.id
            WHERE d.genre = :genre
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['genre' => 'Traditional Weddings']);
        $djs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($djs === false) {
            echo "Error retrieving DJs.";
            return;
        }
        
        echo $this->twig->render('test.twig', [
            'translations' => $translations,
            'djs' => $djs,
        ]);
    }
    
    public function showSubcategory($subcategory, $language = 'en')
    {
        $translations = $this->loadTranslations($language);
        
        $sql = "
            SELECT d.*, c.country_name
            FROM djs d
            LEFT JOIN countries c ON d.country_id = c.id
            WHERE d.genre = :subcategory
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['subcategory' => $subcategory]);
        $djs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debugging: Check the content of $djs
        echo '<pre>';
        print_r($djs);
        echo '</pre>';
        
        if ($djs === false) {
            echo "Error retrieving DJs for the subcategory.";
            return;
        }
        
        echo $this->twig->render('subcategory.twig', [
            'translations' => $translations,
            'djs' => $djs,
            'subcategory' => $subcategory
        ]);
    }
    
private function loadTranslations($language = 'en')
{
    $file = __DIR__ . "/../translations/{$language}.json";

    if (!file_exists($file)) {
        // Fallback to default language if file does not exist
        $file = __DIR__ . '/../translations/en.json';
    }

    $translations = json_decode(file_get_contents($file), true);

    if (!is_array($translations)) {
        // Handle error if JSON decoding fails
        return [];
    }

    return $translations;
}

}
