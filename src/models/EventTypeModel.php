<?php

namespace App\Models;

use PDO;
use Exception;

class EventTypeModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new event type record
    public function createEventType($name)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO event_types (name)
                VALUES (:name)
            ');
            $stmt->execute([
                ':name' => $name
            ]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to create event type: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve an event type by ID
    public function getEventTypeById($id)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM event_types WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve event type: " . $e->getMessage());
            return false;
        }
    }

    // Retrieve all event types
    public function getAllEventTypes()
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM event_types');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to retrieve event types: " . $e->getMessage());
            return false;
        }
    }

    // Update an event type record
    public function updateEventType($id, $name)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE event_types
                SET name = :name
                WHERE id = :id
            ');
            $stmt->execute([
                ':id' => $id,
                ':name' => $name
            ]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to update event type: " . $e->getMessage());
            return false;
        }
    }

    // Delete an event type record
    public function deleteEventType($id)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM event_types WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            // Handle error
            error_log("Failed to delete event type: " . $e->getMessage());
            return false;
        }
    }
}
