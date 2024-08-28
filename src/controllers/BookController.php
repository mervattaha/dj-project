<?php
namespace App\Controllers;

use App\Models\Booking;
use Exception;

class BookController extends BaseController {
    protected $bookingModel;

    public function __construct($twig, $pdo) {
        parent::__construct($twig, $pdo);
        $this->bookingModel = new Booking($pdo);
    }

    public function showBookingForm() {
        echo $this->twig->render('book.twig');
    }

    public function processBooking() {
        $djId = $_POST['dj_id'] ?? '';
        $userName = $_POST['name'] ?? '';
        $userEmail = $_POST['email'] ?? '';
        $eventDate = $_POST['date'] ?? '';

        if (empty($djId) || empty($userName) || empty($userEmail) || empty($eventDate)) {
            echo $this->twig->render('booking_confirm.twig', ['message' => 'Booking failed! Please fill in all required fields.']);
            return;
        }

        try {
            $this->bookingModel->createBooking($djId, $userName, $userEmail, $eventDate);
            echo $this->twig->render('booking_confirm.twig', ['message' => 'Booking confirmed!']);
        } catch (Exception $e) {
            echo $this->twig->render('booking_confirm.twig', ['message' => 'Booking failed! ' . $e->getMessage()]);
        }
    }
}
