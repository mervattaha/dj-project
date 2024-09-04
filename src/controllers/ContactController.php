<?php

namespace App\Controllers;

use PDO;
use Twig\Environment;

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
        echo $this->twig->render('contact.twig', ['translations' => $this->loadTranslations()]);
    }

    public function handleContactForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';

            if (empty($name) || empty($email) || empty($message)) {
                echo $this->twig->render('contact.twig', [
                    'error' => 'Please fill in all required fields.',
                    'translations' => $this->loadTranslations()
                ]);
                return;
            }

            $stmt = $this->pdo->prepare('
                INSERT INTO contacts (name, email, subject, message) 
                VALUES (?, ?, ?, ?)
            ');

            $stmt->execute([$name, $email, $subject, $message]);

            header('Location: /contact-success');
            exit();
        }
    }

    private function loadTranslations()
    {
        $filePath = __DIR__ . '/../translations/en.json';
        if (file_exists($filePath)) {
            return json_decode(file_get_contents($filePath), true);
        }
        return [];
    }
}
