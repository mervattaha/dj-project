<?php

namespace App\Models;

use PDO;
use Exception;

class TopLocationModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Add a new top location
    public function addTopLocation($citySlug, $cityName)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO top_locations (city_slug, city_name)
                VALUES (:city_slug, :city_name)
            ');
            $stmt->execute([
                ':city_slug' => $citySlug,
                ':city_name' => $cityName
            ]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to add top location: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve a top location by ID
    public function getTopLocationById($id)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT * FROM top_locations WHERE id = :id
            ');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve top location: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all top locations
    public function getAllTopLocations()
    {
        try {
            $stmt = $this->pdo->query('
                SELECT * FROM top_locations
            ');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve top locations: " . $e->getMessage());
            return false;
        }
    }

    // Update a top location by ID
    public function updateTopLocation($id, $citySlug, $cityName)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE top_locations
                SET city_slug = :city_slug, city_name = :city_name
                WHERE id = :id
            ');
            $stmt->execute([
                ':id' => $id,
                ':city_slug' => $citySlug,
                ':city_name' => $cityName
            ]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to update top location: " . $e->getMessage());
            return false;
        }
    }

    // Remove a top location by ID
    public function removeTopLocation($id)
    {
        try {
            $stmt = $this->pdo->prepare('
                DELETE FROM top_locations WHERE id = :id
            ');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to remove top location: " . $e->getMessage());
            return false;
        }
    }
}
