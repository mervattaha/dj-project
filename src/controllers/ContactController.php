<?php

class ContactController
{
    private $pdo;
    private $twig;

    public function __construct($pdo, $twig)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
    }

    public function showContactForm()
    {
        echo $this->twig->render('contact.twig');
    }

    public function handleContactForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // التحقق من البيانات الواردة من النموذج
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';

            // التحقق من صحة البيانات
            if (empty($name) || empty($email) || empty($message)) {
                echo $this->twig->render('contact.twig', ['error' => 'Please fill in all required fields.']);
                return;
            }

            // إدراج البيانات في قاعدة البيانات
            $stmt = $this->pdo->prepare('
                INSERT INTO contacts (name, email, subject, message) 
                VALUES (?, ?, ?, ?)
            ');

            $stmt->execute([$name, $email, $subject, $message]);

            // عرض رسالة النجاح
            echo $this->twig->render('contact_success.twig', ['name' => htmlspecialchars($name)]);
        }
    }
}
