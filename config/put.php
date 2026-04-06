<!DOCTYPE html>
<html>

<body>

<?php
require_once __DIR__ . '/db.php';
$ronde = $_POST["round"];
$datum = $_POST["dateId"];
$wie = strval($_POST["wie"]);
$op = strval($_POST["op"]);
$voor = $_POST["voor"];
$puntentotaalwij = $_POST["PuntenTotaalWij"];
$puntentotaalzij = $_POST["PuntenTotaalZij"];
$team = strval($_POST["Team"]);
$speler1 = strval($_POST["Speler1"]);
$speler2 = strval($_POST["Speler2"]);
$speler3 = strval($_POST["Speler3"]);
$speler4 = strval($_POST["Speler4"]);
$roemwij = $_POST["RoemWij"];
$roemzij = $_POST["RoemZij"];
$puntenwij = $_POST["PuntenWij"];
$puntenzij = $_POST["PuntenZij"];
$wijstatus = strval($_POST["StatusWij"]);
$zijstatus = strval($_POST["StatusZij"]);
$deler = strval($_POST["deler"]);
$verzaker = strval($_POST["Verzaker"]);

$id = $datum * 100 + $ronde;

$con = dbConnect();


$sql  = "INSERT INTO `Test2` (`ID`, `SpelID`, `Hand`) VALUES ('$id', '$datum', '$ronde') ON DUPLICATE KEY UPDATE `SpelID` = '$datum', `Hand` = '44'";

if ($con->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    //goto nogzestienkeer;
    #echo "Error: " . $sql . "<br>" . $conn->error;
}

$sql  = "INSERT INTO `spelrondes` (`ID`, `boernelDateID`, `Hand`, `Speler1`, `Speler2`, `Speler3`, `Speler4`, `Wie`, `Op`, `Voor`, `PuntenWij`, `PuntenZij`, `RoemWij`, `RoemZij`, `TotaalPuntenWij`, `PuntenTotaalZij`, `Team`, `deler`,
`verzaker`, `StatusWij`, `StatusZij`, `ELO1`, `ELO2`, `ELO3`, `ELO4`) VALUES
('$id', '$datum', '$ronde', '$speler1', '$speler2', '$speler3', '$speler4', '$wie', '$op', '$voor', '$puntenwij', '$puntenzij', '$roemwij', '$roemzij', '$puntentotaalwij', '$puntentotaalzij', '$team', '$deler',
'$verzaker', '$wijstatus', '$zijstatus', '1200', '1200', '1200', '1200')
ON DUPLICATE KEY UPDATE `ID` = '$id', `boernelDateID` = '$datum',  `Hand` =  '$ronde',  `Speler1` =  '$speler1',  `Speler2` =  '$speler2',  `Speler3` =  '$speler3',  `Speler4` =  '$speler4',
  `Wie` =  '$wie',  `Op` =  '$op',  `Voor` =  '$voor',  `PuntenWij` =  '$puntenwij',  `PuntenZij` =  '$puntenzij',  `RoemWij` =  '$roemwij',  `RoemZij` =  '$roemzij',  `TotaalPuntenWij` =  '$puntentotaalwij',  `PuntenTotaalZij` =  '$puntentotaalzij',
  `Team` =  '$team',  `deler` =  '$deler',  `verzaker` =  '$verzaker',  `StatusWij` =  '$wijstatus',  `StatusZij` =  '$zijstatus',  `ELO1` =  '1200',  `ELO2` =  '1200',  `ELO3` =  '1200',  `ELO4` =  '1200'";

#$result = mysqli_query($con,$sql);


if ($con->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    //goto nogeenkeer;
    #echo "Error: " . $sql . "<br>" . $conn->error;
}





if ($puntentotaalwij >= 1500 OR $puntentotaalzij >= 1500) {
  $sql="SELECT o.Naam, o.ELO_na, Spelerdata.ID  FROM `Boernel_spel_totaal` o LEFT JOIN `Boernel_spel_totaal` b ON o.Naam = b.Naam AND o.SpelID < b.SpelID INNER JOIN Spelerdata on Spelerdata.Naam = o.Naam WHERE b.SpelID is NULL AND o.Naam = '$speler1' limit 1";
  $result = mysqli_query($con,$sql);
  $row = mysqli_fetch_array($result);
  $speler1d = $row['ELO_na'];
  $speler1id = $row['ID'];
  if (empty($speler1d)) {
    $speler1d =  1200;
  }

  $sql="SELECT o.Naam, o.ELO_na, Spelerdata.ID  FROM `Boernel_spel_totaal` o LEFT JOIN `Boernel_spel_totaal` b ON o.Naam = b.Naam AND o.SpelID < b.SpelID INNER JOIN Spelerdata on Spelerdata.Naam = o.Naam WHERE b.SpelID is NULL AND o.Naam = '$speler2' limit 1";
  $result = mysqli_query($con,$sql);
  $row = mysqli_fetch_array($result);
  $speler2d = $row['ELO_na'];
  $speler2id = $row['ID'];
  if (empty($speler2d)) {
    $speler2d =  1200;
  }

  $sql="SELECT o.Naam, o.ELO_na, Spelerdata.ID  FROM `Boernel_spel_totaal` o LEFT JOIN `Boernel_spel_totaal` b ON o.Naam = b.Naam AND o.SpelID < b.SpelID INNER JOIN Spelerdata on Spelerdata.Naam = o.Naam WHERE b.SpelID is NULL AND o.Naam = '$speler3' limit 1";
  $result = mysqli_query($con,$sql);
  $row = mysqli_fetch_array($result);
  $speler3d = $row['ELO_na'];
  $speler3id = $row['ID'];
  if (empty($speler3d)) {
    $speler3d =  1200;
  }

  $sql="SELECT o.Naam, o.ELO_na, Spelerdata.ID  FROM `Boernel_spel_totaal` o LEFT JOIN `Boernel_spel_totaal` b ON o.Naam = b.Naam AND o.SpelID < b.SpelID INNER JOIN Spelerdata on Spelerdata.Naam = o.Naam WHERE b.SpelID is NULL AND o.Naam = '$speler4' limit 1";
  $result = mysqli_query($con,$sql);
  $row = mysqli_fetch_array($result);
  $speler4d = $row['ELO_na'];
  $speler4id = $row['ID'];
  if (empty($speler4d)) {
    $speler4d =  1200;
  }
  $id1 = $datum * 100 + $speler1id;
  $id2 = $datum * 100 + $speler2id;
  $id3 = $datum * 100 + $speler3id;
  $id4 = $datum * 100 + $speler4id;
  $kans1 = 1/(1+pow(10,((($speler3d+$speler4d)-($speler1d+$speler2d))/400)));
  $kans2 = (1 - $kans1);
if ($puntentotaalwij == $puntentotaalzij) {
  $speler1n = $speler1d;
  $speler2n = $speler2d;
  $speler3n = $speler3d;
  $speler4n = $speler4d;
} elseif ($puntentotaalwij > $puntentotaalzij) {
    if ($puntentotaalzij < 500) {
    $factor = 3;
  } elseif ($puntentotaalzij < 1000) {
    $factor = 2;
  } else {
    $factor = 1;
  }
  $speler1n = $speler1d + ceil(32 * $kans2 * ($speler2d/($speler1d+$speler2d)) * 2 * $factor);
  $speler2n = $speler2d + ceil(32 * $kans2 * ($speler1d/($speler1d+$speler2d)) * 2 * $factor);
  $speler3n = $speler3d - ceil(32 * $kans2 * ($speler3d/($speler3d+$speler4d)) * 2 * $factor);
  $speler4n = $speler4d - ceil(32 * $kans2 * ($speler4d/($speler3d+$speler4d)) * 2 * $factor);
} else {
    if ($puntentotaalwij < 500) {
    $factor = 3;
  } elseif ($puntentotaalwij < 1000) {
    $factor = 2;
  } else {
    $factor = 1;
  }
  $speler1n = $speler1d - ceil(32 * $kans1 * ($speler1d/($speler1d+$speler2d)) * 2 * $factor);
  $speler2n = $speler2d - ceil(32 * $kans1 * ($speler2d/($speler1d+$speler2d)) * 2 * $factor);
  $speler3n = $speler3d + ceil(32 * $kans1 * ($speler4d/($speler3d+$speler4d)) * 2 * $factor);
  $speler4n = $speler4d + ceil(32 * $kans1 * ($speler3d/($speler3d+$speler4d)) * 2 * $factor);
}
  $sql  = "INSERT INTO `Boernel_spel_totaal` (`ID`, `SpelID`, `Naam`, `Maat`, `Zij1`, `Zij2`, `PuntenWij`, `PuntenZij`, `ELO_voor`, `ELO_maat`, `ELO_zij1`, `ELO_zij2`, `Kans`, `ELO_na`)
  VALUES ('$id1', '$datum', '$speler1', '$speler2', '$speler3', '$speler4', '$puntentotaalwij', '$puntentotaalzij', '$speler1d', '$speler2d', '$speler3d', '$speler4d', '$kans1', '$speler1n')
  ON DUPLICATE KEY UPDATE `SpelID` = '$datum'";
  #$result = mysqli_query($con,$sql);

  nogtweekeer:

  if ($con->query($sql) === TRUE) {
      echo "New record created successfully";
  } else {
      //goto nogtweekeer;
      #echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $sql  = "INSERT INTO `Boernel_spel_totaal` (`ID`, `SpelID`, `Naam`, `Maat`, `Zij1`, `Zij2`, `PuntenWij`, `PuntenZij`, `ELO_voor`, `ELO_maat`, `ELO_zij1`, `ELO_zij2`, `Kans`, `ELO_na`)
  VALUES ('$id2', '$datum', '$speler2', '$speler1', '$speler3', '$speler4', '$puntentotaalwij', '$puntentotaalzij', '$speler2d', '$speler1d', '$speler3d', '$speler4d', '$kans1', '$speler2n')
  ON DUPLICATE KEY UPDATE `SpelID` = '$datum'";
  #$result = mysqli_query($con,$sql);

  nogvierkeer:

  if ($con->query($sql) === TRUE) {
      echo "New record created successfully";
  } else {
      //goto nogvierkeer;
      #echo "Error: " . $sql . "<br>" . $conn->error;
  }
  $sql  = "INSERT INTO `Boernel_spel_totaal` (`ID`, `SpelID`, `Naam`, `Maat`, `Zij1`, `Zij2`, `PuntenWij`, `PuntenZij`, `ELO_voor`, `ELO_maat`, `ELO_zij1`, `ELO_zij2`, `Kans`, `ELO_na`)
  VALUES ('$id3', '$datum', '$speler3', '$speler4', '$speler1', '$speler2', '$puntentotaalzij', '$puntentotaalwij', '$speler3d', '$speler4d', '$speler1d', '$speler2d', '$kans2', '$speler3n')
  ON DUPLICATE KEY UPDATE `SpelID` = '$datum'";
  #$result = mysqli_query($con,$sql);

  nogvijfkeer:

  if ($con->query($sql) === TRUE) {
      echo "New record created successfully";
  } else {
      //goto nogvijfkeer;
      #echo "Error: " . $sql . "<br>" . $conn->error;
  }
  $sql  = "INSERT INTO `Boernel_spel_totaal` (`ID`, `SpelID`, `Naam`, `Maat`, `Zij1`, `Zij2`, `PuntenWij`, `PuntenZij`, `ELO_voor`, `ELO_maat`, `ELO_zij1`, `ELO_zij2`, `Kans`, `ELO_na`)
  VALUES ('$id4', '$datum', '$speler4', '$speler3', '$speler1', '$speler2', '$puntentotaalzij', '$puntentotaalwij', '$speler4d', '$speler3d', '$speler1d', '$speler2d', '$kans2', '$speler4n')
  ON DUPLICATE KEY UPDATE `SpelID` = '$datum'";
  #$result = mysqli_query($con,$sql);

  nogdriekeer:

  if ($con->query($sql) === TRUE) {
      echo "New record created successfully";
  } else {
      //goto nogdriekeer;
      #echo "Error: " . $sql . "<br>" . $conn->error;
  }

}

mysqli_close($con);

?>
</body>
</html>
