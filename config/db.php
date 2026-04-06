<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'kwwsxpsyzh');
define('DB_PASS', 'PMA8Krqa3N');
define('DB_NAME', 'kwwsxpsyzh');

function dbConnect() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die('Databaseverbinding mislukt: ' . mysqli_connect_error());
    }
    return $conn;
}
