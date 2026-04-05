<!DOCTYPE html>
<html>

<body>

<?php




$con = mysqli_connect('localhost','kwwsxpsyzh','PMA8Krqa3N','kwwsxpsyzh');
if (!$con) {
    echo "Geen verbinding";
}

$sql  = "INSERT INTO Test2 (ID, SpelID, Hand) VALUES (NULL, 1500, 6)";



if ($con->query($sql) === TRUE) {
    echo "Regel aagemaakt";
} else {
    echo "Regel niet aagemaakt";
}



mysqli_close($con);

?>
</body>
</html>
