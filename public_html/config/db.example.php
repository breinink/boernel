<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'db_gebruikersnaam');
define('DB_PASS', 'db_wachtwoord');
define('DB_NAME', 'db_naam');

function dbConnect() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die('Databaseverbinding mislukt: ' . mysqli_connect_error());
    }
    return $conn;
}
