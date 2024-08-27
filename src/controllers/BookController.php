<?php

require_once '../src/models/Booking.php'; // تأكد من صحة المسار

class BookController {
    private $twig;
    private $bookingModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
        $this->bookingModel = new Booking($pdo);
    }

    public function showBookingForm() {
        echo $this->twig->render('book.twig');
    }

    public function processBooking() {
        // الحصول على بيانات النموذج
        $djId = $_POST['dj_id'] ?? '';
        $userName = $_POST['user_name'] ?? '';
        $eventDate = $_POST['event_date'] ?? '';
        $eventType = $_POST['event_type'] ?? '';
        $duration = $_POST['duration'] ?? '';
        $price = $_POST['price'] ?? '';

        // التحقق من أن جميع الحقول موجودة
        if (empty($djId) || empty($userName) || empty($eventDate) || empty($eventType) || empty($duration) || empty($price)) {
            // عرض رسالة خطأ إذا كانت الحقول غير مكتملة
            echo $this->twig->render('booking_confirm.twig', ['message' => 'Booking failed! Please fill in all required fields.']);
            return;
        }

        // استدعاء وظيفة إدخال الحجز
        try {
            $this->bookingModel->createBooking($djId, $userName, $eventDate, $eventType, $duration, $price);
            // عرض رسالة تأكيد
            echo $this->twig->render('booking_confirm.twig', ['message' => 'Booking confirmed!']);
        } catch (Exception $e) {
            // عرض رسالة خطأ إذا فشلت عملية الحجز
            echo $this->twig->render('booking_confirm.twig', ['message' => 'Booking failed! ' . $e->getMessage()]);
        }
    }
}
