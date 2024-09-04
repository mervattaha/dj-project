<?php
namespace App\Models;

use Exception;

class BookingModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function createBooking($djId, $userName, $eventDate, $eventType, $duration, $price)
    {
        // Validate inputs
        if (empty($djId) || empty($userName) || empty($eventDate) || empty($eventType) || empty($duration) || empty($price)) {
            throw new Exception("All fields are required.");
        }
    
        // Sanitize and validate numeric values
        if (!is_numeric($djId) || !is_numeric($duration) || !is_numeric($price)) {
            throw new Exception("Invalid numeric values.");
        }
    
        // Sanitize event date format (e.g., YYYY-MM-DD)
        $eventDate = date('Y-m-d', strtotime($eventDate));
        if ($eventDate === false) {
            throw new Exception("Invalid event date format.");
        }
    
        // Prepare SQL query
        $query = 'INSERT INTO bookings (dj_id, user_name, event_date, event_type, duration, price, status) VALUES (:dj_id, :user_name, :event_date, :event_type, :duration, :price, :status)';
        $statement = $this->pdo->prepare($query);
        $status = 'pending'; // Default status
    
        // Execute SQL query
        try {
            $statement->execute([
                ':dj_id' => $djId,
                ':user_name' => htmlspecialchars($userName),
                ':event_date' => $eventDate,
                ':event_type' => htmlspecialchars($eventType),
                ':duration' => $duration,
                ':price' => $price,
                ':status' => $status
            ]);
        } catch (\PDOException $e) {
            // Handle database errors
            throw new Exception("Failed to create booking: " . $e->getMessage());
        } catch (Exception $e) {
            // Handle general errors
            throw new Exception("An error occurred: " . $e->getMessage());
        }
    }
    

}