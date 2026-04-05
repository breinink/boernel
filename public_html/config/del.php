<!DOCTYPE html>
<html>

<body>

<?php
$ronde = $_POST["round"];
$datum = $_POST["dateId"];

$con = mysqli_connect('localhost','kwwsxpsyzh','PMA8Krqa3N','kwwsxpsyzh');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

mysqli_select_db($con,"ajax_demo");
$sql  = "DELETE FROM spelrondes WHERE boernelDateID = $datum AND Hand = $ronde";
//$result = mysqli_query($con,$sql);
if ($con->query($sql) === TRUE) {
    echo "Round deleted";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

mysqli_close($con);

?>
</body>
</html>
