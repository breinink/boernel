<?php

$GSA70 = 0;
$GSA80 = 0;
$GSA90 = 0;
$GSA100 = 0;
$GSA110 = 0;
$GSA120 = 0;
$GSA130 = 0;
$GSA140 = 0;
$GSA150 = 0;
$GSA162 = 0;

$GSS70 = 0;
$GSS80 = 0;
$GSS90 = 0;
$GSS100 = 0;
$GSS110 = 0;
$GSS120 = 0;
$GSS130 = 0;
$GSS140 = 0;
$GSS150 = 0;
$GSS162 = 0;

$op = array();
$winst = 0;
$winst_P = 0.0;
$opalles = array();
$handen_tot = 0;

$GH_tot_P = 0.0;
$potten = 0;
$handen = 0;
$handenA = array();

$GS= 0;
$GS_P = 0.0;
$GSA = array();
$GSA_P = 25;

$GST= 0;
$GST_P = 0.0;
$GSTA= array();
$GSTA_P = 50;

$GH = 0;
$GH_P = 0.0;
$GHA = 0;
$GHA_P = 0.0;

$GHT = 0;
$GHT_P = 0.0;
$GHTA = array();
$GHTA_P = 0.0;

$Pit = 0;
$Pit_P = 0.0;
$PitA = 0;
$PitA_P = 0.0;

$PitT = 0;
$PitT_P = 0.0;
$PitTA = 0;
$PitTA_P = 0.0;

$verzaakt = 0;
$verzaakt_P = 0.0;

$naam = $_GET["speler"];
$foto = "fotos/";
$foto .= $naam;
$foto .= ".png";
if (file_exists($foto)) {
  //niets
} else {
  $foto = "fotos/geen.png";
}

$connect = mysqli_connect('localhost','kwwsxpsyzh','PMA8Krqa3N','kwwsxpsyzh');
$query ="SELECT DISTINCT spelrondes.ID, spelrondes.Speler1, spelrondes.Speler2, spelrondes.Speler3, spelrondes.Speler4, spelrondes.Wie, spelrondes.Op, spelrondes.Voor, spelrondes.Team, spelrondes.PuntenWij, spelrondes.PuntenZij, spelrondes.StatusWij, spelrondes.StatusZij
FROM spelrondes
    INNER JOIN Boernel_spel_totaal on Boernel_spel_totaal.SpelID = spelrondes.boernelDateID
ORDER BY spelrondes.ID";
$result = mysqli_query($connect, $query);
while($row = mysqli_fetch_array($result))
{
    $handen_tot = $handen_tot + 1;
    if($row["StatusWij"]=='P'){
      $PitA = $PitA + 1;
    }
    if($row["StatusZij"]=='P'){
      $PitA = $PitA + 1;
    }

    if($row["Speler1"]==$naam){
      if($row["StatusWij"]=='P'){
        $PitT = $PitT + 1;
      }
      $handen = $handen + 1;
      if($row["Team"]=='wij'){
        $GST = $GST + 1;
        if($row["PuntenWij"]>0){
          $GHT = $GHT + 1;
        }
      }
    }
    if($row["Speler2"]==$naam){
      $handen = $handen + 1;
      if($row["StatusWij"]=='P'){
        $PitT = $PitT + 1;
      }
      if($row["Team"]=='wij'){
        $GST = $GST + 1;
        if($row["PuntenWij"]>0){
          $GHT = $GHT + 1;
        }
      }
    }
    if($row["Speler3"]==$naam){
      $handen = $handen + 1;
      if($row["StatusZij"]=='P'){
        $PitT = $PitT + 1;
      }
      if($row["Team"]=='zij'){
        $GST = $GST + 1;
        if($row["PuntenZij"]>0){
          $GHT = $GHT + 1;
        }
      }
    }
    if($row["Speler4"]==$naam){
      $handen = $handen + 1;
      if($row["StatusZij"]=='P'){
        $PitT = $PitT + 1;
      }
      if($row["Team"]=='zij'){
        $GST = $GST + 1;
        if($row["PuntenZij"]>0){
          $GHT = $GHT + 1;
        }
      }
    }
    if($row["Wie"]==$naam){
      $GS = $GS + 1;
      if($row["Voor"]==70){
        $GSS70 = $GSS70 + 1;
      }
      if($row["Voor"]==80){
        $GSS80 = $GSS80 + 1;
      }
      if($row["Voor"]==90){
        $GSS90 = $GSS90 + 1;
      }
      if($row["Voor"]==100){
        $GSS100 = $GSS100 + 1;
      }
      if($row["Voor"]==110){
        $GSS110 = $GSS110 + 1;
      }
      if($row["Voor"]==120){
        $GSS120 = $GSS120 + 1;
      }
      if($row["Voor"]==130){
        $GSS130 = $GSS130 + 1;
      }
      if($row["Voor"]==140){
        $GSS140 = $GSS140 + 1;
      }
      if($row["Voor"]==150){
        $GSS150 = $GSS150 + 1;
      }
      if($row["Voor"]==162){
        $GSS162 = $GSS162 + 1;
      }
      if($row["StatusWij"]=='P'){
        $Pit = $Pit + 1;
      }
      if($row["StatusZij"]=='P'){
        $Pit = $Pit + 1;
      }
      if($row["Team"]=='wij'){
        if($row["PuntenWij"]>0){
          $GH = $GH + 1;
        }
      }
      if($row["Team"]=='zij'){
        if($row["PuntenZij"]>0){
          $GH = $GH + 1;
        }
      }


    }
      if($row["Voor"]==70){
        $GSA70 = $GSA70 + 1;
      }
      if($row["Voor"]==80){
        $GSA80 = $GSA80 + 1;
      }
      if($row["Voor"]==90){
        $GSA90 = $GSA90 + 1;
      }
      if($row["Voor"]==100){
        $GSA100 = $GSA100 + 1;
      }
      if($row["Voor"]==110){
        $GSA110 = $GSA110 + 1;
      }
      if($row["Voor"]==120){
        $GSA120 = $GSA120 + 1;
      }
      if($row["Voor"]==130){
        $GSA130 = $GSA130 + 1;
      }
      if($row["Voor"]==140){
        $GSA140 = $GSA140 + 1;
      }
      if($row["Voor"]==150){
        $GSA150 = $GSA150 + 1;
      }
      if($row["Voor"]==162){
        $GSA162 = $GSA162 + 1;
      }
      if($row["Team"]=='wij'){
        if($row["PuntenWij"]>0){
          $GHA = $GHA + 1;
        }
      }
      if($row["Team"]=='zij'){
        if($row["PuntenZij"]>0){
          $GHA = $GHA + 1;
        }
      }





}
$GS70 = round($GSS70/$GS*100,1);
$GS80 = round($GSS80/$GS*100,1);
$GS90 = round($GSS90/$GS*100,1);
$GS100 = round($GSS100/$GS*100,1);
$GS110 = round($GSS110/$GS*100,1);
$GS120 = round($GSS120/$GS*100,1);
$GS130 = round($GSS130/$GS*100,1);
$GS140 = round($GSS140/$GS*100,1);
$GS150 = round($GSS150/$GS*100,1);
$GS162 = round($GSS162/$GS*100,1);
$GS_P = round($GS/$handen*100,1);
$GST_P = round($GST/$handen*100,1);
$GH_P = round((($GH/$GS))*100,1);
$GHT_P = round((($GHT/$GST))*100,1);
$GHA_P = round((($GHA/$handen_tot))*100,1);
$Pit_P = round($Pit/$GS*100,1);
$PitT_P = round($PitT/$GST*100,1);
$PitA_P = round($PitA/$handen_tot*100,1);

$pot = 0;

$GSA70_P = round($GSA70/$handen_tot*100,1);
$GSA80_P = round($GSA80/$handen_tot*100,1);
$GSA90_P = round($GSA90/$handen_tot*100,1);
$GSA100_P = round($GSA100/$handen_tot*100,1);
$GSA110_P = round($GSA110/$handen_tot*100,1);
$GSA120_P = round($GSA120/$handen_tot*100,1);
$GSA130_P = round($GSA130/$handen_tot*100,1);
$GSA140_P = round($GSA140/$handen_tot*100,1);
$GSA150_P = round($GSA150/$handen_tot*100,1);
$GSA162_P = round($GSA162/$handen_tot*100,1);

$P_zelf = array();
$P_gem = array();

$P_zelf[] = $GS70;
$P_zelf[] = $GS80;
$P_zelf[] = $GS90;
$P_zelf[] = $GS100;
$P_zelf[] = $GS110;
$P_zelf[] = $GS120;
$P_zelf[] = $GS130;
$P_zelf[] = $GS140;
$P_zelf[] = $GS150;
$P_zelf[] = $GS162;

$P_gem[] = $GSA70_P;
$P_gem[] = $GSA80_P;
$P_gem[] = $GSA90_P;
$P_gem[] = $GSA100_P;
$P_gem[] = $GSA110_P;
$P_gem[] = $GSA120_P;
$P_gem[] = $GSA130_P;
$P_gem[] = $GSA140_P;
$P_gem[] = $GSA150_P;
$P_gem[] = $GSA162_P;


$query2 ="SELECT * FROM Boernel_spel_totaal ORDER BY SpelID ASC";
$result2 = mysqli_query($connect, $query2);
$laatste = 0;
$ELO = 1200;
$ELOs = array();
$ELOs[] = 1200;

while($row = mysqli_fetch_array($result2))
{
    if($row["Naam"]==$naam) {
      $ELO = $row["ELO_na"];
      $ELOs[] = $row["ELO_na"];
      $pot = $pot + 1;
      $potten = $potten + 1;
      if($row["PuntenWij"]>$row["PuntenZij"]){
        $winst = $winst + 1;
        $laatste =  $laatste + 1;

      } else {
        $laatste = 0;
      }

    }

}
$ELO_A = round(array_sum($ELOs)/count($ELOs),0);
$winst_P = round(($winst / $pot) * 100,1);
$query2 ="SELECT Maat, count(DISTINCT SpelID) as Potten, sum(if(PuntenWij>PuntenZij,1,0))/count(DISTINCT SpelID)*100 as Winst FROM `Boernel_spel_totaal` WHERE Naam = '$naam' GROUP by Maat";
$result2 = mysqli_query($connect, $query2);

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


  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" />
  <script src="https://code.highcharts.com/highcharts.js"></script>

  <script src="https://code.highcharts.com/modules/export-data.js"></script>
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>

<style>
.nopaddingl {
   padding-left: 0 !important;
}
.nopaddingr {
   padding-right: 0 !important;
}

.highcharts-figure, .highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
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
      <div class="col-xs-6 text-left">
        <a href="stats.php" class="btn btn-info btn-lg" role="button">Ranglijst</a>
    </div>
    <div class="col-xs-6 text-right">
      <a href="charts.php?speler=<?php print_r($naam);?>" class="btn btn-warning btn-lg" role="button">ELO-ontwikkeling</a>
      </div>
    </div>
    <div>
      <br>
      <div class="row">
      <div class="col-xs-12 text-center">
        <h3><b><font color="#777777"><?php print_r($naam);?></font></b></h3>
      </div>
      </div>
      <div class="row">
        <div class="col-xs-5 text-right nopaddingr">
          <img src="<?php echo $foto; ?>" class="img-thumbnail img-responsive" alt="foto">
          </div>
          <div class="col-xs-4 text-left">
      <h4>Potten:</h4>
      <h4>Winst:</h4>
      <h4>Win streak:</h4>
      <h4>ELO:</h4>
      <h4>ELO (gem.):</h4>

    </div>
    <div class="col-xs-3 text-left nopaddingl">
      <h4><?php print_r($potten);?></h4>
      <h4><?php print_r($winst_P);?>%</h4>
      <h4><?php print_r($laatste);?></h4>
      <h4><?php print_r($ELO);?></h4>
      <h4><?php print_r($ELO_A);?></h4>
    </div>
    </div>
    </div>
    <h4><font color="#777777">Bieden/halen</font></h4>
    <div class="container-fluid text-center">

                <?php
                if($potten > 4)
                {
                     echo '
                     <div class="table-responsive">
                     <table id="stats" class="table table-striped table-bordered">
                          <thead>
                               <tr>
                                    <td><b>Statistiek</b></td>
                                    <td><b>'.$naam.'</b></td>
                                    <td><b>'.$naam.'<br>& maat</b></td>
                               </tr>
                          </thead>
                     <tr>
                         <td><b>Gespeeld %</b><br>Gem.</td>
                         <td><b>'.$GS_P.'%</b><br>'.((round($GS_P-$GSA_P,1)>0)?'<font color="green">+'.round($GS_P-$GSA_P,1).'%</font>':((round($GS_P-$GSA_P,1)<0)?'<font color="red">'.round($GS_P-$GSA_P,1).'%</font>':''.round($GS_P-$GSA_P,1).'%')).'</td>
                         <td><b>'.$GST_P.'</b>%<br>'.((round($GST_P-$GSTA_P,1)>0)?'<font color="green">+'.round($GST_P-$GSTA_P,1).'%</font>':((round($GST_P-$GSTA_P,1)<0)?'<font color="red">'.round($GST_P-$GSTA_P,1).'%</font>':''.round($GST_P-$GSTA_P,1).'%')).'</td>
                     </tr>
                     <tr>
                         <td><b>Gehaald %</b><br>Gem. ('.$GHA_P.'%)</td>
                         <td><b>'.$GH_P.'%</b><br>'.((round($GH_P-$GHA_P,1)>0)?'<font color="green">+'.round($GH_P-$GHA_P,1).'%</font>':((round($GH_P-$GHA_P,1)<0)?'<font color="red">'.round($GH_P-$GHA_P,1).'%</font>':''.round($GH_P-$GHA_P,1).'%')).'</td>
                         <td><b>'.$GHT_P.'%</b><br>'.((round($GHT_P-$GHA_P,1)>0)?'<font color="green">+'.round($GHT_P-$GHA_P,1).'%</font>':((round($GHT_P-$GHA_P,1)<0)?'<font color="red">'.round($GHT_P-$GHA_P,1).'%</font>':''.round($GHT_P-$GHA_P,1).'%')).'</td>
                     </tr>
                     <tr>
                         <td><b>Pit %</b><br>Gem. ('.$PitA_P.'%)</td>
                         <td><b>'.$Pit_P.'%</b><br>'.((round($Pit_P-$PitA_P,1)>0)?'<font color="green">+'.round($Pit_P-$PitA_P,1).'%</font>':((round($Pit_P-$PitA_P,1)<0)?'<font color="red">'.round($Pit_P-$PitA_P,1).'%</font>':''.round($Pit_P-$PitA_P,1).'%')).'</td>
                         <td><b>'.$PitT_P.'%</b><br>'.((round($PitT_P-$PitA_P,1)>0)?'<font color="green">+'.round($PitT_P-$PitA_P,1).'%</font>':((round($PitT_P-$PitA_P,1)<0)?'<font color="red">'.round($PitT_P-$PitA_P,1).'%</font>':''.round($PitT_P-$PitA_P,1).'%')).'</td>
                     </tr>
                      </table>
                      </div>

                      <div class="row">
                      <div class="col-xs-12 text-center">
                      <h4><font color="#777777">Verdeling biedingen</font></h4>
                      <figure class="highcharts-figure">
                      <div id="container"></div>

                      </figure>
                      </div>
                      </div>



                      <h4><font color="#777777">Gespeeld met</font></h4>
                      <div class="table-responsive">
                           <table id="maat" class="table table-striped table-bordered">
                                <thead>
                                     <tr>
                                          <td><b>Maat</b></td>
                                          <td><b>Potten</b></td>
                                          <td><b>Winst %</b></td>
                                     </tr>
                                </thead> ';

                                while($row = mysqli_fetch_array($result2))
                                {
                                     echo '
                                     <tr>
                                         <td>'.$row["Maat"].'</td>
                                         <td>'.$row["Potten"].'</td>
                                         <td>'.'<div class="progress">
  <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="'.round($row["Winst"],0).'" aria-valuemin="0" aria-valuemax="100" style="width: '.round($row["Winst"],0).'%; color: black;">'.round($row["Winst"],0).'%
  </div>
</div>'.'</td>
                                     </tr>
                                     ';
                                   }
                            echo '


                           </table>
                      </div>';




                   } else{
                     echo '<h4>Niet genoeg potten gespeeld</h4>';
                   }

                ?>


</div>



  <p class="footer text-center text-muted">
    © 2018 - <?php echo date("Y"); ?> <a href="mailto:breinink@gmail.com">brein inc.</a> &nbsp;
  </p>
  </div>
  </div>
</body>
</html>
<script>
var Datazelf = new Array();
var Datagem = new Array();
Datazelf = <?php echo json_encode($P_zelf); ?>;
Datagem = <?php echo json_encode($P_gem); ?>;
var Naam = 'Zelf';
Naam = <?php echo json_encode($naam); ?>;

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

Highcharts.chart('container', {
    chart: {
        type: 'column',
        backgroundColor: null
    },
    title: {
        text: ''
    },
    xAxis: {
        categories: [
            '70',
            '80',
            '90',
            '100',
            '110',
            '120',
            '130',
            '140',
            '150',
            '162'
        ],
        title: {
            text: 'Bod'
        },
        crosshair: true
    },
    yAxis: {
      title: {
          text: ''
      },
        min: 0,
        labels: {
                formatter: function () {
                    return this.axis.defaultLabelFormatter.call(this) + '%';
                }
            }
    },
    credits: {
       enabled: false
     },
    tooltip: {
        headerFormat: '<span style="font-size:10px">Bod van {point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y: .1f}%</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.1,
            borderWidth: 0
        }
    },
    series: [{
        name: Naam,
        data: Datazelf,
        color: '#EB984E'

    }, {
        name: 'Gemiddeld',
        data: Datagem,
        color: '#5499C7'

    }]
});
function toggleDark(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';localStorage.setItem('boernel-theme',isDark?'light':'dark');window.location.reload();}
document.addEventListener('DOMContentLoaded',function(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';var icon=document.getElementById('darkIcon');if(icon)icon.className=isDark?'fas fa-sun':'fas fa-moon';});
</script>
