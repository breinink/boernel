<?php
require_once __DIR__ . '/config/db.php';


$wij = array();
$wij[] = 0;
$zij = array();
$zij[] = 0;
$zijnaam = '';
$wijnaam = '';
$p1 = '';
$p2 = '';
$p3 = '';
$p4 = '';
$ID = 0;
$elo1 = 1200;
$elo2 = 1200;
$elo3 = 1200;
$elo4 = 1200;

$game = 0;
$game = (int)$_GET["game"];
$connect = dbConnect();
$query ="Select * from spelrondes t inner join (SELECT Hand,MAX(ID) as MaxID FROM spelrondes WHERE boernelDateID = $game GROUP BY Hand)a on a.Hand = t.Hand and a.MaxID = ID";
$result = mysqli_query($connect, $query);
while($row = mysqli_fetch_array($result))
{
    $wij[] = (int)$row["TotaalPuntenWij"];
    $zij[] = (int)$row["PuntenTotaalZij"];
    $wijnaam = $row["Speler1"].' & '.$row["Speler2"];
    $zijnaam = $row["Speler3"].' & '.$row["Speler4"];
    $p1 = $row["Speler1"];
    $p2 = $row["Speler2"];
    $p3 = $row["Speler3"];
    $p4 = $row["Speler4"];

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
$foto4 = "fotos/";
$foto4 .= $p4;
$foto4 .= ".png";
if (file_exists($foto4)) {
  //niets
} else {
  $foto4 = "fotos/geen.png";
}

$query2 ="SELECT Boernel_spel_totaal.Naam, Boernel_spel_totaal.SpelID, Boernel_spel_totaal.ELO_na FROM Boernel_spel_totaal INNER JOIN Spelerdata ON Boernel_spel_totaal.Naam = Spelerdata.Naam WHERE SpelID < $game ORDER BY Boernel_spel_totaal.Naam, Boernel_spel_totaal.SpelID ASC";
$result2 = mysqli_query($connect, $query2);

while($row = mysqli_fetch_array($result2))
{
    if ($row["Naam"] === $p1) {
      $elo1 = (int)$row["ELO_na"];
    }
    if ($row["Naam"] === $p2) {
      $elo2 = (int)$row["ELO_na"];
    }
    if ($row["Naam"] === $p3) {
      $elo3 = (int)$row["ELO_na"];
    }
    if ($row["Naam"] === $p4) {
      $elo4 = (int)$row["ELO_na"];
    }



}
$kans1 = 0;
$kans2 = 0;
$kans1 = round((1/(1+pow(10,((($elo3+$elo4)-($elo1+$elo2))/400))))*100);
$kans2 = (100 - $kans1);


$query ="Select * from spelrondes t inner join (SELECT Hand,MAX(ID) as MaxID FROM spelrondes WHERE boernelDateID = $game GROUP BY Hand)a on a.Hand = t.Hand and a.MaxID = ID";
$result = mysqli_query($connect, $query);
$tekst = $wijnaam . ' vs ' .$zijnaam;
?>
<!DOCTYPE html>
<html>
<head>
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
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/series-label.js"></script>

  <script src="https://code.highcharts.com/highcharts-more.js"></script>
  <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" />

<style>
.nopaddingl {
   padding-left: 0 !important;
}
.nopaddingr {
   padding-right: 0 !important;
}
</style>
  <link href="bestanden/bernard.css" rel="stylesheet">
  <script>(function(){var t=localStorage.getItem('boernel-theme');if(!t&&window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)t='dark';if(t==='dark')document.documentElement.setAttribute('data-theme','dark');})();</script>
</head>
<body>

 <div class="container-fluid text-center" style="min-width: 400px; height: 400px; margin: 0 auto">

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
        <p>
          <ul class="nav nav-pills text-left">
          <button type="button" class="btn btn-primary btn-lg" onclick="goBack()">Ga terug</button>
          </ul>
        </p>
    </div>
    </div>
    <div class="row">


        <div class="col-xs-3 nopaddingr">
        <img src="<?php
echo $foto1; ?>" class="img-rounded img-responsive pull-right" alt="Speler 1">
        </div>
        <div class="col-xs-3 nopaddingl">
        <img src="<?php
echo $foto2; ?>" class="img-rounded img-responsive pull-left" alt="Speler 2">
      </div>



      <div class="col-xs-3 nopaddingr">
      <img src="<?php
echo $foto3; ?>" class="img-rounded img-responsive pull-right" alt="Speler 3">
      </div>
      <div class="col-xs-3 nopaddingl">
      <img src="<?php
echo $foto4; ?>" class="img-rounded img-responsive pull-left" alt="Speler 4">


    </div>
    </div>
    <div class="row">
    <div class="col-xs-6 text-left">
    <h4><font color="#EB984E"><?php
echo $wijnaam?><br><?php echo $kans1?>%</font></h4>
    </div>

    <div class="col-xs-6 text-right">
    <h4><font color="#5499C7"><?php
echo $zijnaam?><br><?php echo $kans2?>%</font></h4>
    </div>
  </div>
<div class="container-fluid text-center">
<!--<h4><font color="#EB984E"><?php
echo $wijnaam?></font> vs. <font color="#5499C7"><?php echo $zijnaam?></font></h4>-->

</div>

<div id="container" style="min-width: 300px; height: 400px; margin: 0 auto"></div>
<div class="table-responsive">
     <table id="handengespeeld" class="table table-striped table-bordered">
          <thead>
               <tr>
                    <td>Hand</td>
                    <td>Wie speelt?</td>
                    <td><font color="#EB984E"><?php
echo $p1?> &<br><?php echo $p2?></font></td>
                    <td><font color="#5499C7"><?php
echo $p3?> &<br><?php echo $p4?></font></td>
               </tr>
          </thead>
          <?php
          while($row = mysqli_fetch_array($result))
          {

               echo '
               <tr>
                   <td>'.$row["Hand"].'</td>
                   <td>'.(($row["Team"]=="wij")?'<font color="#EB984E"':'<font color="#5499C7"').'>'.$row["Wie"].' ('.$row["Voor"].') '.'</font><br>'.(($row["StatusWij"]=="G")?'<font color="green">'.'Gehaald'.'</font>':"").(($row["StatusWij"]=="P")?'<font color="green">'.'Pit'.'</font>':"").(($row["Team"]=="wij"&&$row["StatusWij"]=="V")?'<font color="red">'.'Verzaakt ('.$row["verzaker"].')</font>':"").(($row["Team"]=="wij"&&$row["StatusZij"]=="V")?'<font color="green">'.'Verzaakt ('.$row["verzaker"].')</font>':"").(($row["StatusWij"]=="N")?'<font color="red">'.'Nat'.'</font>':"").(($row["StatusZij"]=="G")?'<font color="green">'.'Gehaald'.'</font>':"").(($row["StatusZij"]=="P")?'<font color="green">'.'Pit'.'</font>':"").(($row["Team"]=="zij"&&$row["StatusZij"]=="V")?'<font color="red">'.'Verzaakt ('.$row["verzaker"].')</font>':"").(($row["Team"]=="zij"&&$row["StatusWij"]=="V")?'<font color="green">'.'Verzaakt ('.$row["verzaker"].')</font>':"").(($row["StatusZij"]=="N")?'<font color="red">'.'Nat'.'</font>':"").'</td>
                   <td>'.$row["PuntenWij"].(($row["RoemWij"]>0)?' + '.$row["RoemWij"]:'').'<br><b>'.(($row["TotaalPuntenWij"]>$row["PuntenTotaalZij"])?'<font color="green">'.$row["TotaalPuntenWij"].'</font>':"").(($row["TotaalPuntenWij"]<$row["PuntenTotaalZij"])?'<font color="red">'.$row["TotaalPuntenWij"].'</font>':"").(($row["TotaalPuntenWij"]==$row["PuntenTotaalZij"])?'<font color="black">'.$row["TotaalPuntenWij"].'</font>':"").'</b></td>
                   <td>'.$row["PuntenZij"].(($row["RoemZij"]>0)?' + '.$row["RoemZij"]:'').'<br><b>'.(($row["TotaalPuntenWij"]<$row["PuntenTotaalZij"])?'<font color="green">'.$row["PuntenTotaalZij"].'</font>':"").(($row["TotaalPuntenWij"]>$row["PuntenTotaalZij"])?'<font color="red">'.$row["PuntenTotaalZij"].'</font>':"").(($row["TotaalPuntenWij"]==$row["PuntenTotaalZij"])?'<font color="black">'.$row["PuntenTotaalZij"].'</font>':"").'</b></td>
               </tr>
               ';
             }
          ?>
     </table>
</div>

  <p class="footer text-center text-muted">
    © 2018 - <?php
echo date("Y"); ?> <a href="mailto:breinink@gmail.com">brein inc.</a> &nbsp;
  </p>
  </div>
</body>
</html>


<script>
var isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
if (isDarkMode) {
    Highcharts.setOptions({
        chart:   { style: { color: '#e0e0e0' } },
        title:   { style: { color: '#e0e0e0' } },
        subtitle:{ style: { color: '#aaa' } },
        xAxis: {
            labels: { style: { color: '#ccc' } },
            title:  { style: { color: '#ccc' } },
            gridLineColor: '#3a3a55',
            lineColor: '#4a4a6a',
            tickColor: '#4a4a6a'
        },
        yAxis: {
            labels: { style: { color: '#ccc' } },
            title:  { style: { color: '#ccc' } },
            gridLineColor: '#3a3a55',
            lineColor: '#4a4a6a'
        },
        legend: {
            itemStyle:      { color: '#e0e0e0' },
            itemHoverStyle: { color: '#fff' }
        },
        tooltip: {
            backgroundColor: '#2a2a3e',
            style:           { color: '#e0e0e0' },
            borderColor:     '#3a3a55'
        }
    });
}

var Datawij = new Array();
var Datazij = new Array();
var DatawijT = new Array();
var DatazijT = new Array();
var Nodig = 20;
var Handen = 1;
var ScoreWij = 0;
var ScoreZij = 0;
Datawij = <?php
echo json_encode($wij); ?>;
Datazij = <?php
echo json_encode($zij); ?>;
var Naamwij = 'test';
var Naamzij = 'test';
Naamwij = <?php
echo json_encode($wijnaam); ?>;
Naamzij = <?php
echo json_encode($zijnaam); ?>;
ScoreWij = Datawij[Datawij.length - 1];
ScoreZij = Datazij[Datazij.length - 1];
Handen = (Datawij.length - 1);



if (ScoreWij < 1500 && ScoreZij < 1500 ) {
  DatawijT.push([Handen, ScoreWij]);
  DatazijT.push([Handen, ScoreZij]);
  Nodig = Math.min(Math.ceil(1500 / (ScoreWij / Handen)), Math.ceil(1500 / (ScoreZij / Handen)));
  var i;
  for (i = Datazij.length; i < (Nodig + 1); i++) {
    DatawijT.push([i,Math.ceil(i * (Datawij[Datawij.length - 1]/(Datawij.length - 1)))]);
    DatazijT.push([i,Math.ceil(i * (Datazij[Datazij.length - 1]/(Datazij.length - 1)))]);
}
}
Highcharts.chart('container', {
      chart: {
      backgroundColor: null,
    },

    title: {
        text: ''
    },

    legend: {
        enabled: false
    },
    tooltip: {
    formatter: function () {
        return 'De score na hand <b>' + this.x + '</b> is <b>' + this.y + '</b>';
    }
},


    yAxis: {
    plotLines:[{
    value:1500,
    color: 'green',
    width:2,
    zIndex:1,
    label:{text:'Doel'}
}],
        title: {
            text: ''
        }
    },
    xAxis: {
      gridLineWidth: 1,
      allowDecimals: false,
        title: {
            text: ''
        }
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            pointStart: 0
        },
        line : {
    dataLabels : {
      enabled : true,
      formatter: function() {
        var last  = this.series.data[this.series.data.length - 1];
        if (this.point.category === last.category  && this.point.y === last.y) {
          return this.point.y;
        }
        return "";
      }
    }
  }
    },

    series: [
      {
        name: Naamwij,
        data: Datawij,
        color: '#EB984E',
        dataLabels: {
          enabled: false
        }

      },
      {
        name: Naamzij,
        data: Datazij,
        color: '#5499C7',
        dataLabels: {
          enabled: false
        }
      },
      {
        name: Naamwij,
        data: DatawijT,
        dashStyle: "Dot",
        color: '#EB984E',
        label:{
          enabled: false
        }
      },
      {
        name: Naamzij,
        data: DatazijT,
        dashStyle: "Dot",
        color: '#5499C7',
        label:{
          enabled: false
        }
      }
  ],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});

function goBack() {
    window.history.back();
}
setInterval(function() {
      window.location.reload();
    },  60000);
function toggleDark(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';localStorage.setItem('boernel-theme',isDark?'light':'dark');window.location.reload();}
document.addEventListener('DOMContentLoaded',function(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';var icon=document.getElementById('darkIcon');if(icon)icon.className=isDark?'fas fa-sun':'fas fa-moon';});
</script>
