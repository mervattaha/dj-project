<?php

namespace App\Models;

use PDO;
use Exception;

class EventSubcategoryModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new event subcategory record
    public function createSubcategory($categoryId, $name, $slug)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO event_subcategories (category_id, name, slug)
                VALUES (:category_id, :name, :slug)
            ');
            $stmt->execute([
                ':category_id' => $categoryId,
                ':name' => $name,
                ':slug' => $slug
            ]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to create event subcategory: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve an event subcategory by ID
    public function getSubcategoryById($id)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM event_subcategories WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve event subcategory: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all event subcategories
    public function getAllSubcategories()
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM event_subcategories');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve event subcategories: " . $e->getMessage());
            return false;
        }
    }

    // Update an event subcategory record
    public function updateSubcategory($id, $categoryId, $name, $slug)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE event_subcategories
                SET category_id = :category_id, name = :name, slug = :slug
                WHERE id = :id
            ');
            $stmt->execute([
                ':id' => $id,
                ':category_id' => $categoryId,
                ':name' => $name,
                ':slug' => $slug
            ]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to update event subcategory: " . $e->getMessage());
            return false;
        }
    }

    // Delete an event subcategory record
    public function deleteSubcategory($id)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM event_subcategories WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to delete event subcategory: " . $e->getMessage());
            return false;
        }
    }

    // Find an event subcategory by slug
    public function getSubcategoryBySlug($slug)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM event_subcategories WHERE slug = :slug');
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve event subcategory by slug: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all subcategories for a specific category
    public function getSubcategoriesByCategory($categoryId)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM event_subcategories WHERE category_id = :category_id');
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve subcategories by category: " . $e->getMessage());
            return false;
        }
    }
}
