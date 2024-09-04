<?php

function loadTranslations($langCode) {
    $filePath = __DIR__ . "/translations/$langCode.json";
    echo "File Path: " . $filePath . "<br>";
    if (file_exists($filePath)) {
        $json = file_get_contents($filePath);
        return json_decode($json, true);
    }
    return [];
}
