<?php
function getCityByCoordinates($latitude, $longitude, $pdo) {
    $statement = $pdo->prepare('
        SELECT city 
        FROM cities 
        WHERE ST_Distance_Sphere(point(longitude, latitude), point(:longitude, :latitude)) < 50000
        LIMIT 1
    ');
    $statement->execute([
        'latitude' => $latitude,
        'longitude' => $longitude
    ]);
    return $statement->fetchColumn();
}
?>
