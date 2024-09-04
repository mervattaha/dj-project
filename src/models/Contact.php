<?php

namespace App\Models;

use PDO;

class Contact
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save($name, $email, $subject, $message)
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$name, $email, $subject, $message]);
    }

    public function getAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM contacts');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
