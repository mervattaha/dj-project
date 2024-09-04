<?php

class ContactController {
    private $twig;
    private $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function processContactForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';

            // أضف هنا منطق إرسال البريد الإلكتروني أو حفظ الرسالة في قاعدة البيانات

            echo "Thank you for contacting us, $name!";
        } else {
            echo "Invalid request.";
        }
    }
}
