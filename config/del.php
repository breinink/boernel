<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
requireToken();

$ronde = (int)$_POST["round"];
$datum = (int)$_POST["dateId"];

$con = dbConnect();

$stmt = $con->prepare("DELETE FROM spelrondes WHERE boernelDateID = ? AND Hand = ?");
$stmt->bind_param("ii", $datum, $ronde);
if ($stmt->execute()) {
    echo "Round deleted";
} else {
    error_log("del.php fout: " . $stmt->error);
    echo "Fout bij verwijderen.";
}
$stmt->close();
$con->close();
