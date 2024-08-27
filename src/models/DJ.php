<?php

class DJ {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllDJs() {
        $stmt = $this->pdo->query("SELECT * FROM djs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDJById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM djs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}


