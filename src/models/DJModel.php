<?php

namespace App\Models;

use PDO;
use Exception;

class DJModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new DJ record
    public function createDJ($name, $genre = null, $price = null, $description = null, $image = null, $cityId = null, $city = null)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO djs (name, genre, price, description, image, city_id, city)
                VALUES (:name, :genre, :price, :description, :image, :city_id, :city)
            ');
            $stmt->execute([
                ':name' => $name,
                ':genre' => $genre,
                ':price' => $price,
                ':description' => $description,
                ':image' => $image,
                ':city_id' => $cityId,
                ':city' => $city
            ]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to create DJ: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve a DJ by ID
    public function getDJById($id)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve DJ: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all DJs
    public function getAllDJs()
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM djs');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve DJs: " . $e->getMessage());
            return false;
        }
    }

    // Update a DJ record
    public function updateDJ($id, $name, $genre = null, $price = null, $description = null, $image = null, $cityId = null, $city = null)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE djs 
                SET name = :name, genre = :genre, price = :price, description = :description, image = :image, city_id = :city_id, city = :city
                WHERE id = :id
            ');
            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':genre' => $genre,
                ':price' => $price,
                ':description' => $description,
                ':image' => $image,
                ':city_id' => $cityId,
                ':city' => $city
            ]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to update DJ: " . $e->getMessage());
            return false;
        }
    }

    // Delete a DJ record
    public function deleteDJ($id)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM djs WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to delete DJ: " . $e->getMessage());
            return false;
        }
    }

    // Search DJs by city name
    public function searchDJsByCity($city)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE city LIKE :city');
            $stmt->bindValue(':city', "%{$city}%");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to search DJs by city: " . $e->getMessage());
            return false;
        }
    }

    // Search DJs by location ID
    public function searchDJsByLocationId($cityId)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM djs WHERE city_id = :city_id');
            $stmt->bindParam(':city_id', $cityId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to search DJs by location ID: " . $e->getMessage());
            return false;
        }
    }
}
