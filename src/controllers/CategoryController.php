<?php

namespace App\Controllers;

use Twig\Environment;
use PDO;
use Exception;

class CategoryController {
    private Environment $twig;
    private PDO $pdo;

    public function __construct(Environment $twig, PDO $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function showCategories(): void {
        try {
            $stmt = $this->pdo->query('SELECT * FROM event_categories');
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo $this->twig->render('event_categories.twig', ['categories' => $categories]);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    public function showSubcategories(string $categorySlug): void {
        try {
            // Get the category based on slug
            $stmt = $this->pdo->prepare('SELECT * FROM event_categories WHERE slug = :slug');
            $stmt->execute(['slug' => $categorySlug]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($category) {
                // Get the subcategories for the retrieved category
                $stmt = $this->pdo->prepare('SELECT * FROM event_subcategories WHERE category_id = :category_id');
                $stmt->execute(['category_id' => $category['id']]);
                $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                echo $this->twig->render('event_subcategories.twig', [
                    'category' => $category,
                    'subcategories' => $subcategories
                ]);
            } else {
                echo $this->twig->render('404.twig', ['message' => 'Category not found']);
            }
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    

    public function showSubcategory(string $categorySlug, string $subcategorySlug): void {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM event_categories WHERE slug = :slug');
            $stmt->execute(['slug' => $categorySlug]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($category) {
                $stmt = $this->pdo->prepare('SELECT * FROM event_subcategories WHERE slug = :subcategorySlug AND category_id = :category_id');
                $stmt->execute([
                    'subcategorySlug' => $subcategorySlug,
                    'category_id' => $category['id']
                ]);
                $subcategory = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($subcategory) {
                    echo $this->twig->render('subcategory.twig', ['subcategory' => $subcategory]);
                } else {
                    echo $this->twig->render('404.twig', ['message' => 'Subcategory not found']);
                }
            } else {
                echo $this->twig->render('404.twig', ['message' => 'Category not found']);
            }
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    private function handleError(Exception $e): void {
        // Log error to a file
        error_log($e->getMessage(), 3, '/path/to/error.log');
        echo "An error occurred. Please try again later.";
    }
    
}
