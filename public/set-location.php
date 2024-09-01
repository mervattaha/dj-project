<?php
// set-location.php

session_start();

if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $_SESSION['user_location'] = [
        'latitude' => $latitude,
        'longitude' => $longitude
    ];

    echo 'Location updated successfully';
} else {
    echo 'Location data not received';
}

