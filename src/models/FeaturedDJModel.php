<?php

namespace App\Models;

use PDO;
use Exception;

class FeaturedDJModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Add a DJ to the featured DJs list
    public function addFeaturedDJ($djId)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO featured_djs (dj_id)
                VALUES (:dj_id)
            ');
            $stmt->execute([
                ':dj_id' => $djId
            ]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to add featured DJ: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve a featured DJ by ID
    public function getFeaturedDJById($id)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT * FROM featured_djs WHERE id = :id
            ');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve featured DJ: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all featured DJs
    public function getAllFeaturedDJs()
    {
        try {
            $stmt = $this->pdo->query('
                SELECT * FROM featured_djs
            ');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve featured DJs: " . $e->getMessage());
            return false;
        }
    }

    // Remove a DJ from the featured DJs list
    public function removeFeaturedDJ($id)
    {
        try {
            $stmt = $this->pdo->prepare('
                DELETE FROM featured_djs WHERE id = :id
            ');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to remove featured DJ: " . $e->getMessage());
            return false;
        }
    }

    // Check if a DJ is featured
    public function isFeatured($djId)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT COUNT(*) FROM featured_djs WHERE dj_id = :dj_id
            ');
            $stmt->bindParam(':dj_id', $djId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to check if DJ is featured: " . $e->getMessage());
            return false;
        }
    }
}
