<?php
// HEADERS
switch ($_SERVER['HTTP_ORIGIN']) {
    case 'https://klaverjassen.herokuapp.com': case 'https://spelen.boernel.nl':
    header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    break;
}

// INPUT
$input 		= json_decode(file_get_contents('php://input'), true);
$handData 	= $input;

// DB
// INVULLEN MET JOUW DB CONFIG
$db = array(
	'host' => 'db.boernel.nl',
	'user' => 'md439190db423194',
	'name' => 'md439190db423194',
	'pass' => 'm87V31wjEpU44Hv'
);
$conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);

if ($conn->connect_error) {
	echo 'Connection failed: ' . $conn->connect_error;
	die();
}
elseif (handExists($conn, $handData)) {
	$conn->close();
	echo 'Hand already exists';
	die();
}
elseif ( ! insertHand($conn, $handData)) {
	$conn->close();
	echo 'Error while inserting hand';
	die();
}
else {
	echo 'Hand inserted';
	/// ELO KUNSTJE IN ONDERSTAANDE FUNCTIE
	processELO($conn, $handData);
	$conn->close();
}

function convertInput($input) {
	return array(
		'ronde' => (int)$input['round'],
		'datum' => (int)$input['dateId'],
		'wie' => $input['wie'],
		'op' => $input['op'],
		'voor' => (int)$input['voor'],
		'puntentotaalwij' => (int)$input['PuntenTotaalWij'],
		'puntentotaalzij' => (int)$input['PuntenTotaalZij'],
		'team' => $input['Team'],
		'speler1' => $input['Speler1'],
		'speler2' => $input['Speler2'],
		'speler3' => $input['Speler3'],
		'speler4' => $input['Speler4'],
		'roemwij' => (int)$input['RoemWij'],
		'roemzij' => (int)$input['RoemZij'],
		'puntenwij' => (int)$input['PuntenWij'],
		'puntenzij' => (int)$input['PuntenZij'],
		'wijstatus' => $input['StatusWij'],
		'zijstatus' => $input['StatusZij'],
		'deler' => $input['deler'],
		'verzaker' => $input['Verzaker']
	);
}

function handExists($conn, $handData) {
	$sql = 'SELECT boernelDateID, Hand
			FROM spelrondes_test
			WHERE boernelDateID = ' . $handData['datum'] . '
			AND Hand = ' . $handData['ronde'];

	$result = $conn->query($sql);
	return ($result->num_rows > 0);
}

function insertHand($conn, $handData) {
	$sql =
		'INSERT INTO spelrondes_test (
      ID,
			boernelDateID,
			Hand,
			Speler1,
			Speler2,
			Speler3,
			Speler4,
			Wie,
			Op,
			Voor,
			PuntenWij,
			PuntenZij,
			RoemWij,
			RoemZij,
			TotaalPuntenWij,
			PuntenTotaalZij,
			Team,
			deler,
			verzaker,
			StatusWij,
			StatusZij,
      ELO1,
      ELO2,
      ELO3,
      ELO4
		)
		VALUES (null,
			' . $handData['datum'] . ',
			' . $handData['ronde'] . ',
			"' . $handData['speler1'] . '",
			"' . $handData['speler3'] . '",
			"' . $handData['speler2'] . '",
			"' . $handData['speler4'] . '",
			"' . $handData['wie'] . '",
			"' . $handData['op'] . '",
			' . $handData['voor'] . ',
			' . $handData['puntenwij'] . ',
			' . $handData['puntenzij'] . ',
			' . $handData['roemwij'] . ',
			' . $handData['roemzij'] . ',
			' . $handData['puntentotaalwij'] . ',
			' . $handData['puntentotaalzij'] . ',
			"' . $handData['team'] . '",
			"' . $handData['deler'] . '",
			"' . $handData['verzaker'] . '",
			"' . $handData['wijstatus'] . '",
			"' . $handData['zijstatus'] . '",
      1200,
      1200,
      1200,
      1200
		)
	';

	return $conn->query($sql);
}


function processELO($conn, $handData) {

}



?>
