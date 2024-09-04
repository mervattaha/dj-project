<?php

namespace App\Models;

use PDO;
use Exception;

class EventCategoryModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new event category record
    public function createCategory($name, $slug, $imagePath = null)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO event_categories (name, slug, image_path)
                VALUES (:name, :slug, :image_path)
            ');
            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':image_path' => $imagePath
            ]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to create event category: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve an event category by ID
    public function getCategoryById($id)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM event_categories WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve event category: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all event categories
    public function getAllCategories()
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM event_categories');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve event categories: " . $e->getMessage());
            return false;
        }
    }

    // Update an event category record
    public function updateCategory($id, $name, $slug, $imagePath = null)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE event_categories
                SET name = :name, slug = :slug, image_path = :image_path
                WHERE id = :id
            ');
            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':slug' => $slug,
                ':image_path' => $imagePath
            ]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to update event category: " . $e->getMessage());
            return false;
        }
    }

    // Delete an event category record
    public function deleteCategory($id)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM event_categories WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to delete event category: " . $e->getMessage());
            return false;
        }
    }

    // Find an event category by slug
    public function getCategoryBySlug($slug)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM event_categories WHERE slug = :slug');
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve event category by slug: " . $e->getMessage());
            return false;
        }
    }
}
