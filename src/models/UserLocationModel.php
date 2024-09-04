<?php

namespace App\Models;

use PDO;
use Exception;

class UserLocationModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Add a new user location
    public function addUserLocation($userId, $latitude, $longitude)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO user_locations (user_id, latitude, longitude)
                VALUES (:user_id, :latitude, :longitude)
            ');
            $stmt->execute([
                ':user_id' => $userId,
                ':latitude' => $latitude,
                ':longitude' => $longitude
            ]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to add user location: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve a user location by ID
    public function getUserLocationById($id)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT * FROM user_locations WHERE id = :id
            ');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve user location: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all locations for a specific user
    public function getLocationsByUserId($userId)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT * FROM user_locations WHERE user_id = :user_id
                ORDER BY timestamp DESC
            ');
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve locations by user ID: " . $e->getMessage());
            return false;
        }
    }

    // Update a user location by ID
    public function updateUserLocation($id, $latitude, $longitude)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE user_locations
                SET latitude = :latitude, longitude = :longitude
                WHERE id = :id
            ');
            $stmt->execute([
                ':id' => $id,
                ':latitude' => $latitude,
                ':longitude' => $longitude
            ]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to update user location: " . $e->getMessage());
            return false;
        }
    }

    // Remove a user location by ID
    public function removeUserLocation($id)
    {
        try {
            $stmt = $this->pdo->prepare('
                DELETE FROM user_locations WHERE id = :id
            ');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to remove user location: " . $e->getMessage());
            return false;
        }
    }
}
