<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
requireToken();

$ronde         = (int)$_POST["round"];
$datum         = (int)$_POST["dateId"];
$wie           = strval($_POST["wie"]);
$op            = strval($_POST["op"]);
$voor          = (int)$_POST["voor"];
$puntentotaalwij = (int)$_POST["PuntenTotaalWij"];
$puntentotaalzij = (int)$_POST["PuntenTotaalZij"];
$team          = strval($_POST["Team"]);
$speler1       = strval($_POST["Speler1"]);
$speler2       = strval($_POST["Speler2"]);
$speler3       = strval($_POST["Speler3"]);
$speler4       = strval($_POST["Speler4"]);
$roemwij       = (int)$_POST["RoemWij"];
$roemzij       = (int)$_POST["RoemZij"];
$puntenwij     = (int)$_POST["PuntenWij"];
$puntenzij     = (int)$_POST["PuntenZij"];
$wijstatus     = strval($_POST["StatusWij"]);
$zijstatus     = strval($_POST["StatusZij"]);
$deler         = strval($_POST["deler"]);
$verzaker      = strval($_POST["Verzaker"]);

$id = $datum * 100 + $ronde;

$con = dbConnect();

// Insert hand into Test2
$stmt = $con->prepare("INSERT INTO `Test2` (`ID`, `SpelID`, `Hand`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `SpelID` = ?, `Hand` = 44");
$stmt->bind_param("iiii", $id, $datum, $ronde, $datum);
$stmt->execute();
$stmt->close();

// Insert hand into spelrondes
$stmt = $con->prepare("INSERT INTO `spelrondes` (`ID`, `boernelDateID`, `Hand`, `Speler1`, `Speler2`, `Speler3`, `Speler4`, `Wie`, `Op`, `Voor`, `PuntenWij`, `PuntenZij`, `RoemWij`, `RoemZij`, `TotaalPuntenWij`, `PuntenTotaalZij`, `Team`, `deler`, `verzaker`, `StatusWij`, `StatusZij`, `ELO1`, `ELO2`, `ELO3`, `ELO4`)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1200, 1200, 1200, 1200)
ON DUPLICATE KEY UPDATE `ID`=?, `boernelDateID`=?, `Hand`=?, `Speler1`=?, `Speler2`=?, `Speler3`=?, `Speler4`=?, `Wie`=?, `Op`=?, `Voor`=?, `PuntenWij`=?, `PuntenZij`=?, `RoemWij`=?, `RoemZij`=?, `TotaalPuntenWij`=?, `PuntenTotaalZij`=?, `Team`=?, `deler`=?, `verzaker`=?, `StatusWij`=?, `StatusZij`=?");
$stmt->bind_param(
    "iiissssssiiiiiiisssssiiiissssssiiiiiiisssss",
    $id, $datum, $ronde, $speler1, $speler2, $speler3, $speler4,
    $wie, $op, $voor, $puntenwij, $puntenzij, $roemwij, $roemzij,
    $puntentotaalwij, $puntentotaalzij, $team, $deler, $verzaker, $wijstatus, $zijstatus,
    $id, $datum, $ronde, $speler1, $speler2, $speler3, $speler4,
    $wie, $op, $voor, $puntenwij, $puntenzij, $roemwij, $roemzij,
    $puntentotaalwij, $puntentotaalzij, $team, $deler, $verzaker, $wijstatus, $zijstatus
);
$stmt->execute();
$stmt->close();

// ELO berekening alleen bij einde spel
if ($puntentotaalwij >= 1500 || $puntentotaalzij >= 1500) {

    // Haal ELO op per speler
    $elo_query = "SELECT o.Naam, o.ELO_na, Spelerdata.ID FROM `Boernel_spel_totaal` o
        LEFT JOIN `Boernel_spel_totaal` b ON o.Naam = b.Naam AND o.SpelID < b.SpelID
        INNER JOIN Spelerdata ON Spelerdata.Naam = o.Naam
        WHERE b.SpelID IS NULL AND o.Naam = ? LIMIT 1";

    $spelers = [$speler1, $speler2, $speler3, $speler4];
    $elo = [];
    $ids = [];
    foreach ($spelers as $sp) {
        $stmt = $con->prepare($elo_query);
        $stmt->bind_param("s", $sp);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $elo[] = !empty($row['ELO_na']) ? (int)$row['ELO_na'] : 1200;
        $ids[] = !empty($row['ID']) ? (int)$row['ID'] : 0;
    }

    [$speler1d, $speler2d, $speler3d, $speler4d] = $elo;
    [$speler1id, $speler2id, $speler3id, $speler4id] = $ids;

    $id1 = $datum * 100 + $speler1id;
    $id2 = $datum * 100 + $speler2id;
    $id3 = $datum * 100 + $speler3id;
    $id4 = $datum * 100 + $speler4id;

    $kans1 = 1 / (1 + pow(10, (($speler3d + $speler4d) - ($speler1d + $speler2d)) / 400));
    $kans2 = 1 - $kans1;

    if ($puntentotaalwij == $puntentotaalzij) {
        $speler1n = $speler1d; $speler2n = $speler2d;
        $speler3n = $speler3d; $speler4n = $speler4d;
    } elseif ($puntentotaalwij > $puntentotaalzij) {
        $factor = $puntentotaalzij < 500 ? 3 : ($puntentotaalzij < 1000 ? 2 : 1);
        $speler1n = $speler1d + ceil(32 * $kans2 * ($speler2d / ($speler1d + $speler2d)) * 2 * $factor);
        $speler2n = $speler2d + ceil(32 * $kans2 * ($speler1d / ($speler1d + $speler2d)) * 2 * $factor);
        $speler3n = $speler3d - ceil(32 * $kans2 * ($speler3d / ($speler3d + $speler4d)) * 2 * $factor);
        $speler4n = $speler4d - ceil(32 * $kans2 * ($speler4d / ($speler3d + $speler4d)) * 2 * $factor);
    } else {
        $factor = $puntentotaalwij < 500 ? 3 : ($puntentotaalwij < 1000 ? 2 : 1);
        $speler1n = $speler1d - ceil(32 * $kans1 * ($speler1d / ($speler1d + $speler2d)) * 2 * $factor);
        $speler2n = $speler2d - ceil(32 * $kans1 * ($speler2d / ($speler1d + $speler2d)) * 2 * $factor);
        $speler3n = $speler3d + ceil(32 * $kans1 * ($speler4d / ($speler3d + $speler4d)) * 2 * $factor);
        $speler4n = $speler4d + ceil(32 * $kans1 * ($speler3d / ($speler3d + $speler4d)) * 2 * $factor);
    }

    $elo_insert = "INSERT INTO `Boernel_spel_totaal` (`ID`, `SpelID`, `Naam`, `Maat`, `Zij1`, `Zij2`, `PuntenWij`, `PuntenZij`, `ELO_voor`, `ELO_maat`, `ELO_zij1`, `ELO_zij2`, `Kans`, `ELO_na`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `SpelID` = ?";

    $invoer = [
        [$id1, $datum, $speler1, $speler2, $speler3, $speler4, $puntentotaalwij, $puntentotaalzij, $speler1d, $speler2d, $speler3d, $speler4d, $kans1, $speler1n],
        [$id2, $datum, $speler2, $speler1, $speler3, $speler4, $puntentotaalwij, $puntentotaalzij, $speler2d, $speler1d, $speler3d, $speler4d, $kans1, $speler2n],
        [$id3, $datum, $speler3, $speler4, $speler1, $speler2, $puntentotaalzij, $puntentotaalwij, $speler3d, $speler4d, $speler1d, $speler2d, $kans2, $speler3n],
        [$id4, $datum, $speler4, $speler3, $speler1, $speler2, $puntentotaalzij, $puntentotaalwij, $speler4d, $speler3d, $speler1d, $speler2d, $kans2, $speler4n],
    ];

    foreach ($invoer as $r) {
        $stmt = $con->prepare($elo_insert);
        $stmt->bind_param("iissssiiidddddi", $r[0], $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], $r[8], $r[9], $r[10], $r[11], $r[12], $r[13], $r[1]);
        $stmt->execute();
        $stmt->close();
    }
}

$con->close();
