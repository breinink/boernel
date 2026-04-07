<?php
require_once __DIR__ . '/config/db.php';
$connect = dbConnect();
$tijd = (time() - 365 * 24 * 3600) * 1000;
$query ="SELECT o.Naam, o.ELO_na,o.SpelID FROM `Boernel_spel_totaal` o LEFT JOIN `Boernel_spel_totaal` b ON o.Naam = b.Naam AND o.SpelID < b.SpelID INNER JOIN Spelerdata on Spelerdata.Naam = o.Naam WHERE b.SpelID is NULL ORDER BY o.ELO_na DESC";
$result = mysqli_query($connect, $query);
$tijd2 = (time() - 100 * 24 * 3600) * 1000;
//$query2 ="SELECT p.Naam, p.ELO_na as Toen, x.ELO_na as Nu, x.ELO_na - p.ELO_na as Diff from Boernel_spel_totaal p inner JOIN Boernel_spel_totaal x on x.Naam = p.Naam where p.SpelID = (select max(SpelID) from Boernel_spel_totaal i where i.Naam = p.Naam AND i.SpelID < $tijd2) AND x.SpelID = (select max(SpelID) from Boernel_spel_totaal j where j.Naam = x.Naam) ORDER BY Diff DESC";
//$result2 = mysqli_query($connect, $query2);

?>
<!DOCTYPE html>
<html>
     <head>
          <title>Gronings klaverjasblok - statistieken</title>
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
          <link href="bestanden/bernard.css" rel="stylesheet">
          <script>(function(){var t=localStorage.getItem('boernel-theme');if(!t&&window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)t='dark';if(t==='dark')document.documentElement.setAttribute('data-theme','dark');})();</script>
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
         <a href="index.php" class="btn btn-primary btn-lg" role="button">Home</a>

         <a href="scoreboard.php" class="btn btn-success btn-lg" role="button">Klaverjasblok</a>

         <a href="stats2.php" class="btn btn-info btn-lg" role="button">Alles</a>
         </div>
         </div>

         <div class="row">
           <div class="col-xs-12 ">
             <h4><font color="#777777">Actieve ranglijst</font></h4>
           </div>
           </div>

            <div class="row">
              <div class="col-xs-12 text-left">
          <div class="table-responsive">
               <table id="statistieken" class="table table-striped table-bordered">
                    <thead>
                         <tr>
                              <td>Nr</td>
                              <td>Naam</td>
                              <td>ELO</td>
                         </tr>
                    </thead>
                    <?php
                    $i = 1;
                    $leeg = "";
                    $text = "De scores worden berekend, kom zo terug.";

                    while($row = mysqli_fetch_array($result))
                    {
                      if($row == "") {
                        echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$text.'</td>
                             <td>'.$leeg.'</td>
                        </tr>
                        ';
                        $i = $i + 1;


                      }


                      if($row["ELO_na"] !=  "" && $row["SpelID"] >= $tijd) {
                         echo '
                         <tr>
                             <td>'.$i.'</td>
                              <td><a href="charts2.php?speler='.htmlspecialchars($row["Naam"],ENT_QUOTES,"UTF-8").'">'.htmlspecialchars($row["Naam"],ENT_QUOTES,"UTF-8").'</a>'.(($i==1)?' <font color="#D4AF37"><i class="fa fa-trophy"></i></font>':'').(($i==2)?' <font color="#C0C0C0"><i class="fa fa-medal"></i></font>':'').(($i==3)?' <font color="#cd7f32"><i class="fa fa-medal"></i></font>':'').'</td>
                              <td>'.$row["ELO_na"].'</td>
                         </tr>
                         ';
                         $i = $i + 1;
                     }

                    }
                    ?>
               </table>
          </div>
          </div>
          </div>




                  </div>
                  </div>

               <p class="footer text-center text-muted">
                © 2018 - <?php
echo date("Y"); ?> <a href="mailto:breinink@gmail.com">brein inc.</a> &nbsp;

              </p>



     </body>
</html>
<script>

$(document).ready(function(){
     $('#statistieken').DataTable({
        "searching": false,
        "paging": true,
        "info": false,
        "lengthChange":false,
        "language": {
            "paginate": {
                "previous": "Vorige",
                "next": "Volgende"
            }
        }
    });
});
</script>
<script>
function toggleDark(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';document.documentElement.setAttribute('data-theme',isDark?'light':'dark');localStorage.setItem('boernel-theme',isDark?'light':'dark');document.getElementById('darkIcon').className=isDark?'fas fa-moon':'fas fa-sun';}
document.addEventListener('DOMContentLoaded',function(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';var icon=document.getElementById('darkIcon');if(icon)icon.className=isDark?'fas fa-sun':'fas fa-moon';});
</script>
