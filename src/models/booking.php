<?php
// تأكد من أن هذا هو التعريف الوحيد للفئة Booking
class Booking {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createBooking($djId, $userName, $eventDate, $eventType, $duration, $price) {
        if (empty($djId) || empty($userName) || empty($eventDate) || empty($eventType) || empty($duration) || empty($price)) {
            throw new Exception("All fields are required.");
        }

        $query = 'INSERT INTO bookings (dj_id, user_name, event_date, event_type, duration, price, status) VALUES (:dj_id, :user_name, :event_date, :event_type, :duration, :price, :status)';
        $statement = $this->pdo->prepare($query);
        $status = 'pending'; // قيمة افتراضية للحالة
        $statement->execute([
            ':dj_id' => $djId,
            ':user_name' => $userName,
            ':event_date' => $eventDate,
            ':event_type' => $eventType,
            ':duration' => $duration,
            ':price' => $price,
            ':status' => $status
        ]);
    }
}
