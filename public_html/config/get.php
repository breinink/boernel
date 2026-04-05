<!DOCTYPE html>
<html>

<body>

<?php

$speler1 = strval($_POST["Speler1"]);
$speler2 = strval($_POST["Speler2"]);
$speler3 = strval($_POST["Speler3"]);
$speler4 = strval($_POST["Speler4"]);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


$con = mysqli_connect('localhost','kwwsxpsyzh','PMA8Krqa3N','kwwsxpsyzh');

$sql="SELECT Punten_Na FROM ELO3 WHERE Naam = '$speler1' limit 1";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result);
$speler1d = $row['Punten_Na'];
if (empty($speler1d)) {
  $speler1d =  1300;
}

$sql="SELECT Punten_Na FROM ELO3 WHERE Naam = '$speler2' limit 1";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result);
$speler2d = $row['Punten_Na'];
if (empty($speler2d)) {
  $speler2d =  1300;
}

$sql="SELECT Punten_Na FROM ELO3 WHERE Naam = '$speler3' limit 1";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result);
$speler3d = $row['Punten_Na'];
if (empty($speler3d)) {
  $speler3d =  1300;
}

$sql="SELECT Punten_Na FROM ELO3 WHERE Naam = '$speler4' limit 1";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result);
$speler4d = $row['Punten_Na'];
if (empty($speler4d)) {
  $speler4d =  1300;
}
$ar = array("speler1"=>$speler1d,"speler2"=>$speler2d,"speler3"=>$speler3d,"speler4"=>$speler4d);
echo json_encode($ar);



mysqli_close($con);

?>
</body>
</html>
