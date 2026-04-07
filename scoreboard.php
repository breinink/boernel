<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
$connect = dbConnect();
$query ="SELECT Naam, Sub FROM `Spelerdata` WHERE Actief = 1 ORDER BY Sub ASC, Naam ASC";
$result1 = mysqli_query($connect, $query);
$result2 = mysqli_query($connect, $query);
$result3 = mysqli_query($connect, $query);
$result4 = mysqli_query($connect, $query);
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
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
		<link id="theme-bernard" href="./bestanden/bernard.css" rel="stylesheet">
		<script>(function(){var t=localStorage.getItem('boernel-theme');if(!t&&window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)t='dark';if(t==='dark')document.documentElement.setAttribute('data-theme','dark');})();</script>
		<style>
		input[type="text"]:disabled{background-color:white;}
		.scrollable-menu {
    height: auto;
    max-height: 500px;
    overflow-x: hidden;
}

		</style>
	</head>

	<body style="">

		<div class="container-fluid text-center">

			<!-- Header and Round info -->
			<div class="row">
				<div class="col-xs-4">
					<h4>Boer<span class="text-primary">Nel</span>.nl <small class="hidden-xs">Gronings klaverjasblok</small></h4>
				</div>
				<div class="col-xs-4 text-left">
					<h4><nobr>Hand <span id="round">1</span> (<span id="dealer">deler</span> deelt)</nobr></h4>
				</div>
				<div class="col-xs-1"></div>
				<div class="col-xs-2 text-right">
					<button id="darkToggle" class="btn btn-default" onclick="toggleDark()" title="Wissel thema"><i id="darkIcon" class="fas fa-moon"></i></button>
				</div>
			</div>


			<div class="well well-sm">
				<!-- Main buttons and status info -->


				<!-- Point input fields -->
				<div class="row" id="wie">
					<h5 class="text-centered">Wie speelt?</h5>
					<div class="col-xs-12">
							<button id="speler1" value="1" class="col-xs-6 btn btn-default btn-lg">Speler 1</button>
							<button id="speler2" value="1" class="col-xs-6 btn btn-default btn-lg">Speler 2</button>
							<button id="speler3" value="1" class="col-xs-6 btn btn-default btn-lg">Speler 3</button>
							<button id="speler4" value="1" class="col-xs-6 btn btn-default btn-lg">Speler 4</button>

					</div>
				</div>

				<div class="row" id="op">
					<h5 class="text-centered">Waarop?</h5>
					<div class="col-xs-12">
						<span id="opwelkekleur">
							<button id="harten" value="1" class="col-xs-4 btn btn-default btn-lg">Harten</button>
							<button id="schoppen" value="2" class="col-xs-4 btn btn-default btn-lg">Schoppen</button>
							<button id="ruiten" value="3" class="col-xs-4 btn btn-default btn-lg">Ruiten</button>
							<button id="klaveren" value="4" class="col-xs-4 btn btn-default btn-lg">Klaveren</button>
							<button id="sans" value="5" class="col-xs-4 btn btn-default btn-lg">Sans</button>
						</span>
					</div>
				</div>
				<div class="row" id="voor">
					<h5 class="text-centered">Voor hoeveel?</h5>
					<div class="col-xs-12">
						<span id="voorhoeveel">
							<button id="70" value="70" class="col-xs-3 btn btn-default btn-lg">70</button>
							<button id="80" value="80" class="col-xs-3 btn btn-default btn-lg">80</button>
							<button id="90" value="90" class="col-xs-3 btn btn-default btn-lg">90</button>
							<button id="100" value="100" class="col-xs-3 btn btn-default btn-lg">100</button>
							<button id="110" value="110" class="col-xs-3 btn btn-default btn-lg">110</button>
							<button id="120" value="120" class="col-xs-3 btn btn-default btn-lg">120</button>
							<button id="130" value="130" class="col-xs-3 btn btn-default btn-lg">130</button>
							<button id="140" value="140" class="col-xs-3 btn btn-default btn-lg">140</button>
							<button id="150" value="150" class="col-xs-3 btn btn-default btn-lg">150</button>
							<button id="162" value="162" class="col-xs-3 btn btn-default btn-lg">Pit</button>
						</span>
					</div>
				</div>


				<!-- Score input fields -->

<div class="row" id="Scores">
			<div class="col-xs-12">
				<div class="col-xs-3">
					<input id="roemwij" type="text" placeholder="0" disabled="disabled" class="form-control disabled input" value="">
				</div>
				<div class="col-xs-3">
					<input id="puntenwij" type="number" placeholder="Wij" class="form-control selectonclick input" value="">
				</div>
				<div class="col-xs-3">
					<input id="puntenzij" type="number" placeholder="Zij" class="form-control selectonclick input" value="">
					</div>
				<div class="col-xs-3">
					<input id="roemzij" type="text" placeholder="0" disabled="disabled" class="form-control disabled input" value="">
				</div>
			</div>
</div>
				<!-- Roem input fields -->
				<div class="row" id="Roem">
					<div class="col-xs-6">
						<span id="roemwijbuttons">
							<button value="-1" class="col-xs-4 btn btn-warning btn-lg">Wis</button>
							<button value="20" class="col-xs-4 btn btn-primary btn-lg">20</button>
							<button value="50" class="col-xs-4 btn btn-primary btn-lg">50</button>
							<!--button value="100" class="col-xs-3 btn btn-primary">100</button>-->
						</span>
					</div>
					<div class="col-xs-6">
						<span id="roemzijbuttons" class="form form-inline">
							<button value="-1" class="col-xs-4 btn btn-warning btn-lg">Wis</button>
							<button value="20" class="col-xs-4 btn btn-primary btn-lg">20</button>
							<button value="50" class="col-xs-4 btn btn-primary btn-lg">50</button>
						</span>
					</div>
					</div>
				<!-- Nat, verzaakt en pit -->
				<div class="row" id="Gamebuttons">
					<div class="col-xs-12">
						<button id="nat" value="1" class="col-xs-4 btn btn-danger btn-lg">Nat</button>
						<button id="verzaken" value="1" class="col-xs-4 btn btn-warning btn-lg" data-toggle="modal" data-target="#myModal">Verzaakt</button>
						<button id="pit" value="1" class="col-xs-4 btn btn-success btn-lg">Pit</button>
					</div>
		</div>

<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Wie heeft verzaakt?</h4>
        </div>
        <div class="modal-body">
         			<div class="row" id="wieverzaakt">
					<span id="verzaker">
							<button id="speler1V" value="1" class="col-xs-6 btn btn-default btn-default btn-lg">Speler 1</button>
							<button id="speler2V" value="2" class="col-xs-6 btn btn-default btn-default btn-lg">Speler 2</button>
							<button id="speler3V" value="3" class="col-xs-6 btn btn-default btn-default btn-lg">Speler 3</button>
							<button id="speler4V" value="4" class="col-xs-6 btn btn-default btn-default btn-lg">Speler 4</button>
					</span>
				</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Sluit</button>
        </div>
      </div>

    </div>
  </div>

	<!-- Modal -->
	  <div class="modal fade" id="Spelerinput" role="dialog" data-controls-modal="your_div_id" data-backdrop="static" data-keyboard="false" href="#">
	    <div class="modal-dialog">

	      <!-- Modal spelerinpit-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
	          <h4 class="modal-title">Wie spelen er?</h4>
	        </div>
	        <div class="modal-body">
						<div class="row" id="voor">
							<div class="col-xs-12 text-center">
								<div class="row nav-row">
								<div class="col-xs-3"></div>
								<div class="dropdown-container">
								<button id="Maat" onclick="speler2()" class="col-xs-6 btn btn-default dropdown-toggle btn-lg" type="button" data-toggle="dropdown">Maat
								<span class="maat"></span></button>
								<ul class="dropdown-menu scrollable-menu">
									<?php
									$sub = "";
									$subnr = 0;
				          while($row = mysqli_fetch_array($result1))
				          {
										if($sub!=$row["Sub"]) {
											if($subnr>=1) {
											echo '<li class="divider"></li>';
											}
											echo '<li class="dropdown-header">'.$row["Sub"].'</li>';
											$subnr = $subnr + 1;
										}
										echo '<li><a href="#">'.$row["Naam"].'</a></li>';
										$sub = $row["Sub"];
				             }
				          ?>
									<li class="divider"></li>
									<li class="dropdown-header">Gasten</li>
									<li><a href="#">Gast 1</a></li>
									<li><a href="#">Gast 2</a></li>
									<li><a href="#">Gast 3</a></li>
						    </ul>
							</div>
									<div class="col-xs-3"></div>


							</div>
								<div class="row nav-row">

								<div class="dropdown-container">
						    <button id="West" onclick="speler3()" class="col-xs-6 btn btn-default dropdown-toggle btn-lg" type="button" data-toggle="dropdown">West
						    <span class="west"></span></button>
								<ul class="dropdown-menu scrollable-menu">
									<?php
									$sub = "";
									$subnr = 0;
				          while($row = mysqli_fetch_array($result2))
				          {
										if($sub!=$row["Sub"]) {
											if($subnr>=1) {
											echo '<li class="divider"></li>';
											}
											echo '<li class="dropdown-header">'.$row["Sub"].'</li>';
											$subnr = $subnr + 1;
										}
										echo '<li><a href="#">'.$row["Naam"].'</a></li>';
										$sub = $row["Sub"];
				             }
				          ?>
									<li class="divider"></li>
									<li class="dropdown-header">Gasten</li>
									<li><a href="#">Gast 1</a></li>
									<li><a href="#">Gast 2</a></li>
									<li><a href="#">Gast 3</a></li>
						    </ul>
							</div>

								<div class="dropdown-container">
						    <button id="Oost" onclick="speler4()" class="col-xs-6 btn btn-default dropdown-toggle btn-lg" type="button" data-toggle="dropdown">Oost
						    <span class="oost"></span></button>
								<ul class="dropdown-menu scrollable-menu">
									<?php
									$sub = "";
									$subnr = 0;
				          while($row = mysqli_fetch_array($result3))
				          {
										if($sub!=$row["Sub"]) {
											if($subnr>=1) {
											echo '<li class="divider"></li>';
											}
											echo '<li class="dropdown-header">'.$row["Sub"].'</li>';
											$subnr = $subnr + 1;
										}
										echo '<li><a href="#">'.$row["Naam"].'</a></li>';
										$sub = $row["Sub"];
				             }
				          ?>
									<li class="divider"></li>
									<li class="dropdown-header">Gasten</li>
									<li><a href="#">Gast 1</a></li>
									<li><a href="#">Gast 2</a></li>
									<li><a href="#">Gast 3</a></li>
						    </ul>
							</div>
							</div>
							<div class="row nav-row">

								<div class="col-xs-3"></div>
									<div class="dropdown-container">
								<button id="Teller" onclick="speler1()" class="col-xs-6 btn btn-default dropdown-toggle btn-lg" type="button" data-toggle="dropdown">Teller
						    <span class="teller"></span></button>
						    <ul class="dropdown-menu scrollable-menu">
									<?php
									$sub = "";
									$subnr = 0;
				          while($row = mysqli_fetch_array($result4))
				          {
										if($sub!=$row["Sub"]) {
											if($subnr>=1) {
											echo '<li class="divider"></li>';
											}
											echo '<li class="dropdown-header">'.$row["Sub"].'</li>';
											$subnr = $subnr + 1;
										}
										echo '<li><a href="#">'.$row["Naam"].'</a></li>';
										$sub = $row["Sub"];
				             }
				          ?>
									<li class="divider"></li>
									<li class="dropdown-header">Gasten</li>
									<li><a href="#">Gast 1</a></li>
									<li><a href="#">Gast 2</a></li>
									<li><a href="#">Gast 3</a></li>
						    </ul>
							</div>
								</div>

								<div class="row nav-row" id="test3">
										<button id="boeren" type="button" class="btn btn-primary btn-lg" disabled="disabled">Boeren</button>
								</div>
								<div class="col-xs-12 text-centered">
										<span id="bericht" style="display: none;"><span class="text-success">Er is geboerd!</span></span>
									</div>
								<hr />
								<h4>Wie is de eerste deler?</h4>
								<div class="row nav-row" id="wieisdeler">

										<span id="eerstedeler">
										<button id="speler1D" value="1" class="col-xs-6 btn btn-default btn-lg">Speler 1</button>
										<button id="speler2D" value="2" class="col-xs-6 btn btn-default btn-lg">Speler 2</button>
										<button id="speler3D" value="3" class="col-xs-6 btn btn-default btn-lg">Speler 3</button>
										<button id="speler4D" value="4" class="col-xs-6 btn btn-default btn-lg">Speler 4</button>
										</span>

						</div>
							</div>
					</div>
				</div>
	        <div class="modal-footer">
	          <button id="klaar" type="button" class="btn btn-success btn-lg" data-dismiss="modal">Start spel</button>


	    		</div>
				</div>
	  	</div>

	</div>




					<!-- Process round button -->
					<div class="row" id="Verwerken">
					<div class="col-xs-12">

						<button id="verwerk" class="btn btn-primary btn-lg">Verwerk »</button>
					</div>
					<div class="col-xs-">
						<button id="resetbutton" class="btn btn-danger btn-lg" style="display: none;">Nieuw spel</button>
					</div>
				</div>
				</div>

				<div class="col-xs-12 text-centered">
						<span id="messages" style="display: none;"><span class="text-success">Nieuw spel gereed!</span></span>
					</div>


			<!-- Result table -->
			<div class="row" id="Verwerken">
			<h4>Score-overzicht</h4>
			<table class="table table-condensed table-bordered">
				<thead>
					<tr class="active">
						<th class="text-right">Roem</th>
						<th class="text-right">Wij</th>
						<th class="text-center">Hand</th>
						<th class="text-left">Zij</th>
						<th class="text-left">Roem</th>
					</tr>
				</thead>
				<tbody id="scorelist"></tbody>
			</table>

				<!-- Score info -->
				<div class="row">
					<div class="col-xs-6">
						<h4>Wij: <span id="totaalwij">0</span></h4>
					</div>
					<div class="col-xs-6">
						<h4>Zij: <span id="totaalzij">0</span></h4>
					</div>
				</div>

			<div class="row">
				<div class="col-xs-6 pull-left">
					<button id="undolastmove" class="btn btn-warning btn-lg">« Wis vorige hand</button>
					<br><br>
				</div>
				<div class="col-xs-6 pull-right">
					<button id="resetnow" class="btn btn-danger btn-lg">Reset spel</button>
					<br><br>
				</div>
			</div>

			<hr>

		</div>
		<div class="row">
		<div class="col-xs-6">
			<a href="index.php" class="btn btn-primary btn-lg" role="button">Home</a>
		</div>

		<div class="col-xs-6">
			<a href="stats.php" class="btn btn-info btn-lg" role="button">Ranglijst</a>
		</div>
		</div>
</div>
		<!-- Footer -->




		<p class="footer text-center text-muted">
			© 2018 - <?php
echo date("Y"); ?> <a href="mailto:breinink@gmail.com">brein inc.</a> &nbsp;

		</p>

		<!-- Google Analytics -->


		<!-- JS -->
		<script type="text/javascript" src="./bestanden/jquery.min.js"></script>
		<script type="text/javascript" src="./bestanden/bootstrap.min.js"></script>
		<script>var BOERNEL_TOKEN = '<?php echo API_TOKEN; ?>';</script>
		<script type="text/javascript" src="./bestanden/app.js"></script>
		<script type="text/javascript" src="./bestanden/jquery.cookie.js"></script>

		<div class="modal fade" id="spelidmodal">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Eerder spel voortzetten</h4>
			  </div>
			  <div class="modal-body">
				<p>Spel-ID dat voortgezet dient te worden:</p>
				<p><input class="form-control input-sm col-xs-6" id="nieuwspelid" placeholder="a1b2c3d6"></p>
				<br>
				<p class="text-muted">Let op! Het huidige spel wordt gewist.</p>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Terug</button>
				<button type="button" data-dismiss="modal" id="continuegame" class="btn btn-primary btn-lg">Spel voortzetten »</button>
			  </div>
			</div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<!-- Bevestigingsmodal spelafronding -->
		<div class="modal fade" id="bevestigModal" role="dialog" data-backdrop="static" data-keyboard="false">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h4 class="modal-title"><i class="fas fa-exclamation-triangle text-warning"></i> Let op: het spel eindigt!</h4>
			  </div>
			  <div class="modal-body">
				<p>Met deze score wordt het spel afgerond. Dit kan achteraf niet meer worden gecorrigeerd.</p>
				<p id="bevestigModalScore"></p>
				<p><strong>Heb je de score correct ingevoerd?</strong></p>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Nee, pas aan</button>
				<button type="button" class="btn btn-success btn-lg" id="bevestigJa" data-dismiss="modal">Ja, verwerk &raquo;</button>
			  </div>
			</div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->


<script>
function toggleDark(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';document.documentElement.setAttribute('data-theme',isDark?'light':'dark');localStorage.setItem('boernel-theme',isDark?'light':'dark');document.getElementById('darkIcon').className=isDark?'fas fa-moon':'fas fa-sun';}
document.addEventListener('DOMContentLoaded',function(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';var icon=document.getElementById('darkIcon');if(icon)icon.className=isDark?'fas fa-sun':'fas fa-moon';});
</script>
</body></html>
