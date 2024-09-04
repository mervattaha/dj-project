<?php
namespace App\Controllers;

use Twig\Environment;
use PDO;use
 App\Models\BookingModel; // Ensure you have this import if BookingModel is in a different namespace

class BookController
{
    protected $twig;
    protected $pdo;
    private $bookingModel;

    public function __construct(Environment $twig, PDO $pdo, BookingModel $bookingModel)
    { 
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->bookingModel = $bookingModel;
    }

    public function showBookingForm()
    {
        // عرض نموذج الحجز
        echo $this->twig->render('book.twig');
    }
    public function processBooking()
    {
        $djId = filter_input(INPUT_POST, 'dj_id', FILTER_SANITIZE_NUMBER_INT);
        $userName = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $userEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $eventDate = htmlspecialchars($_POST['date'] ?? '', ENT_QUOTES, 'UTF-8');
        $eventType = htmlspecialchars($_POST['event_type'] ?? '', ENT_QUOTES, 'UTF-8'); // Add this
        $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // Add this
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // Add this
        
        // Validate email address
        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            echo $this->twig->render('booking_confirm.twig', [
                'message' => 'Booking failed! Invalid email address.'
            ]);
            return;
        }
    
        // Validate required fields
        if (empty($djId) || empty($userName) || empty($eventDate) || empty($eventType) || empty($duration) || empty($price)) {
            echo $this->twig->render('booking_confirm.twig', [
                'message' => 'Booking failed! Please fill in all required fields.'
            ]);
            return;
        }
        
        try {
            // Create booking using the model
            $this->bookingModel->createBooking($djId, $userName, $eventDate, $eventType, $duration, $price);
            echo $this->twig->render('booking_confirm.twig', [
                'message' => 'Booking confirmed!'
            ]);
        } catch (\Exception $e) {
            // Handle exceptions and display error message
            echo $this->twig->render('booking_confirm.twig', [
                'message' => 'Booking failed! ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            ]);
        }
    }
    
    
    
}
