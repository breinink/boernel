<?php
require_once __DIR__ . '/db.php';
$connect = dbConnect();
$query ="SELECT * FROM spelrondes WHERE (ID IN (SELECT MAX(ID) FROM spelrondes WHERE boernelDateID > 1534327791000 GROUP BY boernelDateID)) OR (boernelDateID > 1524302591000 AND (TotaalPuntenWij > 1500 OR PuntenTotaalZij > 1500)) ORDER BY boernelDateID DESC";
$result = mysqli_query($connect, $query);
$tijd = time();
?>
<!DOCTYPE html>
<html lang="nl"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<title>Gronings klaverjasblok</title>
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="Bernard Reinink">
		<meta name="robots" content="index, follow">
		<meta name="google-site-verification" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<meta property="og:title" content="">
		<meta property="og:description" content="">
		<meta property="og:image" content="">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<!-- <link rel="shortcut icon" href="" /> -->

		<!-- CSS -->
		<link id="theme-current" href="./bestanden/bootstrap.min.css" rel="stylesheet">
		<link href="./bestanden/font-awesome.css" rel="stylesheet">
		<link id="theme-bernard" href="./bestanden/bernard.css" rel="stylesheet">
		<style type "text/css">
<!--
/* @group Blink */
.blink {
	-webkit-animation: blink .75s linear infinite;
	-moz-animation: blink .75s linear infinite;
	-ms-animation: blink .75s linear infinite;
	-o-animation: blink .75s linear infinite;
	 animation: blink .75s linear infinite;
}
@-webkit-keyframes blink {
	0% { opacity: 1; }
	50% { opacity: 1; }
	50.01% { opacity: 0; }
	100% { opacity: 0; }
}
@-moz-keyframes blink {
	0% { opacity: 1; }
	50% { opacity: 1; }
	50.01% { opacity: 0; }
	100% { opacity: 0; }
}
@-ms-keyframes blink {
	0% { opacity: 1; }
	50% { opacity: 1; }
	50.01% { opacity: 0; }
	100% { opacity: 0; }
}
@-o-keyframes blink {
	0% { opacity: 1; }
	50% { opacity: 1; }
	50.01% { opacity: 0; }
	100% { opacity: 0; }
}
@keyframes blink {
	0% { opacity: 1; }
	50% { opacity: 1; }
	50.01% { opacity: 0; }
	100% { opacity: 0; }
}
/* @end */
-->
</style>

	</head>

	<body style="">

		<div class="container-fluid text-center">

			<!-- Header and Round info -->
			<div class="row">
				<div class="col-xs-12">
					<h1>Boer<span class="text-primary">Nel</span>.nl <br><small>Gronings klaverjasblok</small></h1>
				</div>
			</div>


			<div class="well well-sm">
				<!-- Main buttons and status info -->
				<div class="row">
					<div class="col-xs-12">
				<a href="scoreboard.php" class="btn btn-success btn-lg" role="button">Nieuw/hervat spel</a>
				<a href="stats.php" class="btn btn-primary btn-lg" role="button">Naar stats</a>
				</div>
				</div>
				<hr>
				<h2><small>Potten op Lowlands</small></h2>
				<div class="table-responsive">
				     <table id="potten" class="table table-striped table-bordered">
				          <thead>
				               <tr>
				                    <td><b>Aanvang</b></td>
				                    <td><b>Team A</b></td>
				                    <td><b>Team B</b></td>
				               </tr>
				          </thead>
				          <?php
				          setlocale(LC_TIME, "nl_NL");
				          $i = 1;
				          while($row = mysqli_fetch_array($result))
				          {
				               echo '
				               <tr>
				                   <td><a href="game.php?game='.$row["boernelDateID"].'">'.strftime("%A <br> %k:%M", $row["boernelDateID"]/1000).((($row["boernelDateID"]/1000)>($tijd - 2 * 3600))?'<br><p class="tab blink">Live</p>':'').'</a></td>
				                   <td>'.$row["Speler1"].' &<br>'.$row["Speler2"].':<br><b>'.(($row["TotaalPuntenWij"]>$row["PuntenTotaalZij"])?'<font color="green">'.$row["TotaalPuntenWij"].'</font>':'<font color="red">'.$row["TotaalPuntenWij"].'</font>').'</b></td>
				                   <td>'.$row["Speler3"].' &<br>'.$row["Speler4"].':<br><b>'.(($row["TotaalPuntenWij"]<$row["PuntenTotaalZij"])?'<font color="green">'.$row["PuntenTotaalZij"].'</font>':'<font color="red">'.$row["PuntenTotaalZij"].'</font>').'</b></td>
				               </tr>
				               ';
				             }

				          ?>
				     </table>
				</div>

</div>
</div>

		<!-- Footer -->


		<p class="footer text-center text-muted">
	    © 2018 <a href="mailto:breinink@gmail.com">brein inc.</a> &nbsp;
	  </p>
</body></html>
