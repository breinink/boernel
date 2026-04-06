<!DOCTYPE html>
<html>

<body>

<?php
require_once __DIR__ . '/db.php';




$con = dbConnect();
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
