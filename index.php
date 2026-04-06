<?php
require_once __DIR__ . '/config/db.php';
function formatDatum($timestamp, $format) {
    $maanden = ['jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec'];
    $dagen   = ['ma','di','wo','do','vr','za','zo'];
    $ts = intval($timestamp);
    return strtr($format, [
        '%a' => $dagen[date('N', $ts) - 1],
        '%e' => date('j', $ts),
        '%b' => $maanden[date('n', $ts) - 1],
        '%y' => date('y', $ts),
        '%k' => date('G', $ts),
        '%M' => date('i', $ts),
    ]);
}
$tijd = (time() - 2 * 3600) * 1000;
$tijd2 = (time() - 100 * 24 * 3600) * 1000;
$connect = dbConnect();
$query ="SELECT * FROM spelrondes WHERE (ID IN (SELECT MAX(ID) FROM spelrondes WHERE boernelDateID > '$tijd' GROUP BY boernelDateID)) OR (boernelDateID > '$tijd2' AND (TotaalPuntenWij >= 1500 OR PuntenTotaalZij >= 1500)) ORDER BY boernelDateID DESC";
$result = mysqli_query($connect, $query);
$tijd = (time() - 365 * 24 * 3600) * 1000;
$query2 ="SELECT o.Naam, o.ELO_na,o.SpelID FROM `Boernel_spel_totaal` o LEFT JOIN `Boernel_spel_totaal` b ON o.Naam = b.Naam AND o.SpelID < b.SpelID WHERE b.SpelID is NULL AND o.SpelID > '$tijd' ORDER BY o.ELO_na DESC";
$result2 = mysqli_query($connect, $query2);
$i = 0;
while($row1 = mysqli_fetch_array($result2))
{
		$i=$i+1;
		if($i==1) {
			$p1 = $row1["Naam"];
		}
		if($i==2) {
			$p2 = $row1["Naam"];
		}
		if($i==3) {
			$p3 = $row1["Naam"];
		}

}
$foto1 = "fotos/";
$foto1 .= $p1;
$foto1 .= ".png";
if (file_exists($foto1)) {
  //niets
} else {
  $foto1 = "fotos/geen.png";
}
$foto2 = "fotos/";
$foto2 .= $p2;
$foto2 .= ".png";
if (file_exists($foto2)) {
  //niets
} else {
  $foto2 = "fotos/geen.png";
}
$foto3 = "fotos/";
$foto3 .= $p3;
$foto3 .= ".png";
if (file_exists($foto3)) {
  //niets
} else {
  $foto3 = "fotos/geen.png";
}
$query3 ="select c.Naam, (d.ELO_later - c.ELO_eerder) as ELO_delta
FROM
(SELECT a.Naam, a.ELO_na as ELO_eerder
FROM Boernel_spel_totaal a
INNER JOIN
    (SELECT Naam, MAX(SpelID) AS MaxSpelID
    FROM Boernel_spel_totaal
    WHERE SpelID < '$tijd2' AND Naam NOT LIKE 'Gast 1' AND Naam NOT LIKE 'Gast 2' AND Naam NOT LIKE 'Gast 3'
    GROUP BY Naam) b
ON a.Naam = b.Naam
AND a.SpelID = b.MaxSpelID) c
INNER JOIN
(SELECT z.Naam, z.ELO_na as ELO_later
FROM Boernel_spel_totaal z
INNER JOIN
    (SELECT Naam, MAX(SpelID) AS MaxSpelID
    FROM Boernel_spel_totaal
    WHERE SpelID
    GROUP BY Naam) y
ON z.Naam = y.Naam
AND z.SpelID = y.MaxSpelID) d
ON c.Naam  = d.Naam
ORDER BY ELO_delta DESC";
$result3 = mysqli_query($connect, $query3);
$i = 0;
while($row1 = mysqli_fetch_array($result3))
{
		$i=$i+1;
		if($i==1) {
			$p1 = $row1["Naam"];
			$elo1 = $row1["ELO_delta"];
		}
		if($i==2) {
			$p2 = $row1["Naam"];
			$elo2 = $row1["ELO_delta"];
		}
		if($i==3) {
			$p3 = $row1["Naam"];
			$elo3 = $row1["ELO_delta"];
		}

}
$fotoA1 = "fotos/";
$fotoA1 .= $p1;
$fotoA1 .= ".png";
if (file_exists($fotoA1)) {
  //niets
} else {
  $fotoA1 = "fotos/geen.png";
}
$fotoA2 = "fotos/";
$fotoA2 .= $p2;
$fotoA2 .= ".png";
if (file_exists($fotoA2)) {
  //niets
} else {
  $fotoA2 = "fotos/geen.png";
}
$fotoA3 = "fotos/";
$fotoA3 .= $p3;
$fotoA3 .= ".png";
if (file_exists($fotoA3)) {
  //niets
} else {
  $fotoA3 = "fotos/geen.png";
}
$query4 ="SELECT b.Naam, (b.ELO_na-b.ELO_voor) as ELO_delta, b.SpelID, b.PuntenWij, b.PuntenZij, b.Zij1, b.Zij2
from Boernel_spel_totaal b
INNER JOIN
(SELECT Naam, SpelID, (ELO_na-ELO_voor) AS ELO_plus
    FROM Boernel_spel_totaal
    WHERE SpelID > '$tijd2' and ELO_na > ELO_voor
    GROUP BY Naam, SpelID
ORDER BY ELO_plus DESC
LIMIT 1) a
ON b.SpelID = a.SpelID
WHERE b.ELO_na > b.ELO_voor";
$result4 = mysqli_query($connect, $query4);
$i = 0;
$tekst = "";
$tekst2 = "";
$delta = 0;
$spelid = "";
while($row1 = mysqli_fetch_array($result4))
{
		$i=$i+1;
		if($i==1) {
			$p1 = $row1["Naam"];
			$delta = $row1["ELO_delta"];
			$spelid = $row1["SpelID"];





		}
		if($i==2) {
			$p2 = $row1["Naam"];
			$delta = $delta + $row1["ELO_delta"];
			$tekst = "+";
			$tekst .= $delta;
			$tekst .= " ELO";
			$tekst2 = $row1["PuntenWij"];
			$tekst2 .= " - ";
			$tekst2 .= $row1["PuntenZij"];
			$tekst2 .= " (";
			$tekst2 .= $row1["Zij1"];
			$tekst2 .= " & ";
			$tekst2 .= $row1["Zij2"];
			$tekst2 .= ")";
		}

}
$fotoB1 = "fotos/";
$fotoB1 .= $p1;
$fotoB1 .= ".png";
if (file_exists($fotoB1)) {
  //niets
} else {
  $fotoB1 = "fotos/geen.png";
}
$fotoB2 = "fotos/";
$fotoB2 .= $p2;
$fotoB2 .= ".png";
if (file_exists($fotoB2)) {
  //niets
} else {
  $fotoB2 = "fotos/geen.png";
}
$query5 ="SELECT Naam, (ELO_na-ELO_voor) as ELO_delta, SpelID, PuntenWij, PuntenZij, Zij1, Zij2
FROM `Boernel_spel_totaal`
where ELO_na > ELO_voor AND SpelID > '$tijd2'
ORDER BY SpelID DESC
LIMIT 2";
$result5 = mysqli_query($connect, $query5);
$i = 0;
$tekst3 = "";
$tekst4 = "";
$delta2 = 0;
$spelid2 = "";
while($row1 = mysqli_fetch_array($result5))
{
		$i=$i+1;
		if($i==1) {
			$p1 = $row1["Naam"];
			$delta2 = $row1["ELO_delta"];
			$spelid2 = $row1["SpelID"];





		}
		if($i==2) {
			$p2 = $row1["Naam"];
			$delta2 = $delta2 + $row1["ELO_delta"];
			$tekst3 = "+";
			$tekst3 .= $delta2;
			$tekst3 .= " ELO";
			$tekst4 = $row1["PuntenWij"];
			$tekst4 .= " - ";
			$tekst4 .= $row1["PuntenZij"];
			$tekst4 .= " (";
			$tekst4 .= $row1["Zij1"];
			$tekst4 .= " & ";
			$tekst4 .= $row1["Zij2"];
			$tekst4 .= ")";
		}

}
$fotoC1 = "fotos/";
$fotoC1 .= $p1;
$fotoC1 .= ".png";
if (file_exists($fotoC1)) {
  //niets
} else {
  $fotoC1 = "fotos/geen.png";
}
$fotoC2 = "fotos/";
$fotoC2 .= $p2;
$fotoC2 .= ".png";
if (file_exists($fotoC2)) {
  //niets
} else {
  $fotoC2 = "fotos/geen.png";
}
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

		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
		<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" />

<!-- Bootstrap JS -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<link href="bestanden/bernard.css" rel="stylesheet">
		<script>(function(){var t=localStorage.getItem('boernel-theme');if(!t&&window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)t='dark';if(t==='dark')document.documentElement.setAttribute('data-theme','dark');})();</script>
		<script>
// Initialize tooltip component
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

// Initialize popover component
$(function () {
  $('[data-toggle="popover"]').popover()
})
</script>
<style>
.nopadding {
   padding: 0 !important;
   margin: 0 !important;
}

.carousel-indicators li {
  background-color: transparent;
  border: none;
  width: 36px;
  height: 36px;
  text-indent: 0;
  font-size: 20px;
  line-height: 36px;
  opacity: 0.35;
  margin: 0 4px;
  cursor: pointer;
}
.carousel-indicators .active {
  background-color: transparent;
  width: 36px;
  height: 36px;
  opacity: 1;
}
ol.carousel-indicators {
  position: static;
  margin: 4px 0 0 0;
  width: auto;
}

.row-fluid {
   display: flex;
   align-items: flex-end;
}


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
			<div class="row" style="position: relative;">
				<div class="col-xs-12">
					<h2>Boer<span class="text-primary">Nel</span>.nl <br><small>In God we Trust, all others bring data</small></h2>
				</div>
				<div style="position: absolute; top: 0; right: 0; padding-right: 15px;">
					<button id="darkToggle" class="btn btn-default" onclick="toggleDark()" title="Wissel thema"><i id="darkIcon" class="fas fa-moon"></i></button>
				</div>
			</div>


			<div class="well well-sm">
				<!-- Main buttons and status info -->
				<div class="row">
					<div class="col-xs-12">
				<a href="scoreboard.php" class="btn btn-success btn-lg" role="button">Klaverjasblok</a>
				<!--<a href="https://spelen.boernel.nl" class="btn btn-warning btn-lg" role="button">Jas app</a>-->
				<a href="stats.php" class="btn btn-info btn-lg" role="button">Ranglijst</a>
				</div>
				</div>
				<div class="container-fluid">


<!-- Carousel container -->
<div id="home" class="carousel slide" data-ride="carousel">

<!-- Indicators -->
<ol class="carousel-indicators">
<li data-target="#home" data-slide-to="0" class="active"><i class="fas fa-user-friends" style="color:black;"></i></li>
<li data-target="#home" data-slide-to="1"><i class="fas fa-crown" style="color:#D4AF37;"></i></li>
<li data-target="#home" data-slide-to="2"><i class="fab fa-hotjar" style="color:red;"></i></li>
<li data-target="#home" data-slide-to="3"><i class="fas fa-user-friends" style="color:#D4AF37;"></i></li>
</ol>

<!-- Content -->
<div class="carousel-inner" role="listbox">
<!-- Slide 4 -->
<div class="item active">

				<div class="row ">
				<h4><font color="#777777">Laatste winnaars </font><font color="Black"><i class="fas fa-user-friends"></i></font></h4>
				</div>
				<div class="row row-fluid">
				<div class="col-xs-1">
				</div>


						<div class="col-xs-5">
						<img src="<?php
echo $fotoC1; ?>" class="img-rounded img-responsive center-block" alt="Team p1">

						</div>
						<div class="col-xs-5">
						<img src="<?php
echo $fotoC2; ?>" class="img-rounded img-responsive center-block" alt="Team p2">

					</div>
					<div class="col-xs-1">
					</div>


				</div>

				<div class="row row-fluid">
				<div class="col-xs-4">
				<div class="caption" style="text-align:right;">
				<h4><font color="#D4AF37"><?php
echo $tekst3; ?></font></h4>
				</div>
				</div>
				<div class="col-xs-8">
				<div class="caption" style="text-align:center;">
				<h5><font color="#777777"><?php
echo '<a href="game.php?game='.$spelid2.'">'; ?><?php echo $tekst4; ?></a></font></h5>
				</div>
				</div>
				</div>


				<div class="row">
				<br>
				</div>


</div>
<!-- Slide 1 -->
<div class="item">

<div class="row">
<h4><font color="#777777">Grote spelers </font><font color="#D4AF37"><i class="fas fa-crown"></font></i></h4>
</div>
<div class="row row-fluid">


		<div class="col-xs-4">
		<img src="<?php
echo $foto2; ?>" class="img-rounded img-responsive center-block" alt="Plek 2">
		<div class="caption">
		<h4><font color="#C0C0C0"><i class="fa fa-medal"></i></font></h4>
		</div>


		</div>
		<div class="col-xs-5">
		<img src="<?php
echo $foto1; ?>" class="img-rounded img-responsive center-block" alt="Plek 1">
		<div class="caption">
		<h4><font color="#D4AF37"><i class="fa fa-trophy"></i></font></h4>
		</div>
	</div>



	<div class="col-xs-3">
	<img src="<?php
echo $foto3; ?>" class="img-rounded img-responsive center-block" alt="Plek 3">
	<div class="caption">
	<h4><font color="#cd7f32"><i class="fa fa-medal"></i></font></h4>
	</div>
	</div>

</div>
<div class="row ">
<br>
</div>

</div>
<!-- Slide 2 -->
<div class="item">

				<div class="row ">
				<h4><font color="#777777">Hete spelers </font><font color="red"><i class="fab fa-hotjar"></i></font></h4>
				</div>
				<div class="row row-fluid">


						<div class="col-xs-4">
						<img src="<?php
echo $fotoA2; ?>" class="img-rounded img-responsive center-block" alt="Plek 2">
						<div class="caption">
						<h5><font color="#C0C0C0">+<?php
echo $elo2; ?> ELO</font></h5>
						</div>


						</div>
						<div class="col-xs-5">
						<img src="<?php
echo $fotoA1; ?>" class="img-rounded img-responsive center-block" alt="Plek 1">
						<div class="caption">
						<h4><font color="#D4AF37">+<?php
echo $elo1; ?> ELO</font></h4>
						</div>
					</div>



					<div class="col-xs-3">
					<img src="<?php
echo $fotoA3; ?>" class="img-rounded img-responsive center-block" alt="Plek 3">
					<div class="caption">
					<h5><font color="#cd7f32">+<?php
echo $elo3; ?> ELO</font></h5>
					</div>
					</div>

				</div>
				<div class="row ">
				<br>
				</div>


</div>
<!-- Slide 3 -->
<div class="item">

				<div class="row ">
				<h4><font color="#777777">Beste teamprestatie </font><font color="#D4AF37"><i class="fas fa-user-friends"></i></font></h4>
				</div>
				<div class="row row-fluid">
				<div class="col-xs-1">
				</div>


						<div class="col-xs-5">
						<img src="<?php
echo $fotoB1; ?>" class="img-rounded img-responsive center-block" alt="Team p1">

						</div>
						<div class="col-xs-5">
						<img src="<?php
echo $fotoB2; ?>" class="img-rounded img-responsive center-block" alt="Team p2">

					</div>
					<div class="col-xs-1">
					</div>


				</div>

				<div class="row row-fluid">
				<div class="col-xs-4">
				<div class="caption" style="text-align:right;">
				<h4><font color="#D4AF37"><?php
echo $tekst; ?></font></h4>
				</div>
				</div>
				<div class="col-xs-8">
				<div class="caption" style="text-align:center;">
				<h5><font color="#777777"><?php
echo '<a href="game.php?game='.$spelid.'">'; ?><?php echo $tekst2; ?></a></font></h5>
				</div>
				</div>
				</div>


				<div class="row">
				<br>
				</div>


</div>

</div>


</div>

<!-- Previous/Next controls
<a class="left carousel-control" href="#home" role="button" data-slide="prev">
<span class="icon-prev" aria-hidden="true"></span>
<span class="sr-only">Previous</span>
</a>
<a class="right carousel-control" href="#home" role="button" data-slide="next">
<span class="icon-next" aria-hidden="true"></span>
<span class="sr-only">Next</span>
</a> -->



</div>

				<div class="row">
				<h4><font color="#777777">Potten afgelopen 100 dagen</font></h4>
				</div>
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
				                   <td><a href="game.php?game='.$row["boernelDateID"].'">'.(((idate('W',$row["boernelDateID"]/1000)*10+date('N',$row["boernelDateID"]/1000)>435))?formatDatum($row["boernelDateID"]/1000, "%a %e %b %k:%M"):formatDatum($row["boernelDateID"]/1000, "%a %e %b %k:%M")).((($row["boernelDateID"])>($tijd)&&($row["PuntenTotaalZij"])<(1500)&&($row["TotaalPuntenWij"])<(1500))?'<br><p class="tab blink">Live</p>':'').'</a></td>
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
	    © 2018 - <?php
echo date("Y"); ?> <a href="mailto:breinink@gmail.com">brein inc.</a> &nbsp;
	  </p>
<script>
function toggleDark(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';document.documentElement.setAttribute('data-theme',isDark?'light':'dark');localStorage.setItem('boernel-theme',isDark?'light':'dark');document.getElementById('darkIcon').className=isDark?'fas fa-moon':'fas fa-sun';}
document.addEventListener('DOMContentLoaded',function(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';var icon=document.getElementById('darkIcon');if(icon)icon.className=isDark?'fas fa-sun':'fas fa-moon';});
</script>
</body></html>
