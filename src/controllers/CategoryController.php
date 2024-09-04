<?php

namespace App\Controllers;

use Twig\Environment;
use PDO;
use Exception;


class CategoryController extends BaseController{
    protected  $twig;
    protected  $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function showCategories() {
        try {
            $stmt = $this->pdo->query('SELECT * FROM event_categories');
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo $this->twig->render('event_categories.twig', ['categories' => $categories]);
        } catch (\PDOException $e) {
            echo $this->twig->render('error.twig', ['message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
        } catch (Exception $e) {
            echo $this->twig->render('error.twig', ['message' => 'An unexpected error occurred.']);
        }
    }

    public function showSubcategories($categorySlug) {
        try {
            // Verify and sanitize categorySlug
            $categorySlug = htmlspecialchars($categorySlug);
            
            $stmt = $this->pdo->prepare('SELECT * FROM event_categories WHERE slug = :slug');
            $stmt->execute(['slug' => $categorySlug]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($category) {
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
        } catch (\PDOException $e) {
            echo $this->twig->render('error.twig', ['message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
        } catch (Exception $e) {
            echo $this->twig->render('error.twig', ['message' => 'An unexpected error occurred.']);
        }
    }

    public function showSubcategory($categorySlug, $subcategorySlug) {
        try {
            // Verify and sanitize inputs
            $categorySlug = htmlspecialchars($categorySlug);
            $subcategorySlug = htmlspecialchars($subcategorySlug);
            
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
        } catch (\PDOException $e) {
            echo $this->twig->render('error.twig', ['message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
        } catch (Exception $e) {
            echo $this->twig->render('error.twig', ['message' => 'An unexpected error occurred.']);
        }
    }
}
