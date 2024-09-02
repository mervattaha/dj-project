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
    
        // Fetch subcategories for the "Weddings" category
        $categoryId = $this->getCategoryId('Weddings');
        
        $sqlSubcategories = "
            SELECT id, name, slug
            FROM event_subcategories
            WHERE category_id = :category_id
        ";
    
        $stmtSubcategories = $this->pdo->prepare($sqlSubcategories);
        $stmtSubcategories->execute(['category_id' => $categoryId]);
        $subcategories = $stmtSubcategories->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch DJs for a specific subcategory
        $sqlDJs = "
            SELECT d.*, c.country_name
            FROM djs d
            LEFT JOIN countries c ON d.country_id = c.id
            WHERE d.genre = :genre
        ";
    
        $stmtDJs = $this->pdo->prepare($sqlDJs);
        $stmtDJs->execute(['genre' => 'Traditional Weddings']);
        $djs = $stmtDJs->fetchAll(PDO::FETCH_ASSOC);
    
        // Check if no DJs are found
        $noDJsMessage = empty($djs) ? $translations['no_djs_available'] : '';
    
        // Render the Twig template
        echo $this->twig->render('subcategory.twig', [
            'translations' => $translations,
            'djs' => $djs,
            'subcategories' => $subcategories,
            'noDJsMessage' => $noDJsMessage
        ]);
    }
    
    private function getCategoryId($categoryName)
    {
        $sql = "SELECT id FROM event_categories WHERE name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $categoryName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? $result['id'] : null;
    }
    
    public function showSubcategory($subcategory, $language = 'en')
    {
        $translations = $this->loadTranslations($language);
    
        $sql = "
            SELECT d.*, c.country_name
            FROM djs d
            LEFT JOIN countries c ON d.country_id = c.id
            WHERE d.subcategory = :subcategory
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['subcategory' => $subcategory]);
        $djs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Print query result for debugging
        error_log("Fetched DJs: " . print_r($djs, true));
    
        // Check if no DJs are found
        $noDJsMessage = empty($djs) ? $translations['no_djs_available'] : '';
    
        // Render the Twig template
        echo $this->twig->render('subcategory.twig', [
            'translations' => $translations,
            'djs' => $djs,
            'subcategory' => $subcategory,
            'noDJsMessage' => $noDJsMessage
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
