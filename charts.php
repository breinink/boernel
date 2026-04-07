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

$rows = array();
$rows2 = array();
$rows3 = array();
$win = array();
$handen = array();
$rows[] = 1200;

$win[] = -1;
$naam = isset($_GET["speler"]) ? trim($_GET["speler"]) : '';
if ($naam === '') { die("Ongeldige speler."); }
$connect = dbConnect();
$stmt = $connect->prepare("SELECT * FROM `Boernel_spel_totaal` WHERE `Naam` = ? ORDER BY `SpelID` ASC");
$stmt->bind_param("s", $naam);
$stmt->execute();
$result = $stmt->get_result();
$potten = 0;
$pot_aantal = 0;
$hand_aantal = 0;
$vorigepot = 0;
$gehaald_aantal = 0;
$gespeeld_aantal = 0;


while($row = mysqli_fetch_array($result))
{
    $rows[] = (int)$row["ELO_na"];
    if ((int)$row["PuntenZij"]<(int)$row["PuntenWij"]) {
      $win[] = 1;
    }
    else{
      $win[] = 0;

    }
$potten = $potten + 1;


}
$stmt2 = $connect->prepare("SELECT * FROM `Boernel_spel_totaal` WHERE `Naam` = ? ORDER BY `SpelID` DESC");
$stmt2->bind_param("s", $naam);
$stmt2->execute();
$result2 = $stmt2->get_result();

$stmt3 = $connect->prepare("SELECT Boernel_spel_totaal.SpelID, Boernel_spel_totaal.Maat, spelrondes.ID, spelrondes.Wie, spelrondes.Team, spelrondes.PuntenWij, spelrondes.PuntenZij FROM Boernel_spel_totaal INNER JOIN spelrondes ON Boernel_spel_totaal.SpelID = spelrondes.boernelDateID WHERE Boernel_spel_totaal.Naam = ? ORDER BY Boernel_spel_totaal.SpelID ASC, spelrondes.ID ASC");
$stmt3->bind_param("s", $naam);
$stmt3->execute();
$result3 = $stmt3->get_result();
$rows2[] = null;
$rows3[] = null;
while($row = mysqli_fetch_array($result3))
{
  if ((int)$row["SpelID"]===$vorigepot) {
    $hand_aantal = $hand_aantal + 1;

  } else {
    if ($pot_aantal>0) {
      if ($gespeeld_aantal===0) {
        $rows2[] = 0;
      } else {
        $rows2[] = Round($gehaald_aantal / $gespeeld_aantal * 100,0);
      }
      $rows3[] = Round($gespeeld_aantal / $hand_aantal * 100,0);
    }

    $hand_aantal = 1;
    $pot_aantal = $pot_aantal + 1;
    $gehaald_aantal = 0;
    $gespeeld_aantal = 0;
  }

  if ($row["Wie"]===$naam || $row["Wie"]===$row["Maat"]) {
    $gespeeld_aantal = $gespeeld_aantal + 1;
    if ($row["Team"]==='wij' && (int)$row["PuntenWij"]>0) {
      $gehaald_aantal = $gehaald_aantal + 1;
    }
    if ($row["Team"]==='zij' && (int)$row["PuntenZij"]>0) {
      $gehaald_aantal = $gehaald_aantal + 1;
    }
  }

  $vorigepot = (int)$row["SpelID"];




}
if ($gespeeld_aantal===0) {
  $rows2[] = 0;
} else {
  $rows2[] = Round($gehaald_aantal / $gespeeld_aantal * 100,0);
}

$rows3[] = Round($gespeeld_aantal / $hand_aantal * 100,0);

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
  <script src="//rawgithub.com/phpepe/highcharts-regression/master/highcharts-regression.js"> </script>
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

<div class="container-fluid text-center">
<!--<h4>Test<?php
print_r($handen);?></h4>-->
</div>



<div id="container" style="min-width: 300px; height: 400px; margin: 0 auto"></div>
<div id="container2" style="min-width: 300px; height: 400px; margin: 0 auto"></div>
<div class="table-responsive">
     <table id="pottengespeeld" class="table table-striped table-bordered">
          <thead>
               <tr>
                    <td><b>Pot</b></td>
                    <td><b>Wij</b></td>
                    <td><b>Zij</b></td>
                    <td><b>ELO</b></td>
               </tr>
          </thead>
          <?php
          setlocale(LC_TIME, "nl_NL");
          $i = $potten;
          while($row = mysqli_fetch_array($result2))
          {
               echo '
               <tr>
                    <td><a href="game.php?game='.$row["SpelID"].'">Pot '.$i.':<br>'.formatDatum($row["SpelID"]/1000, "%e %b '%y").'<br>'.formatDatum($row["SpelID"]/1000, "%k:%M").'</a></td>
                    <td>'.$row["Maat"].'<br><br><b>'.(($row["PuntenWij"]>$row["PuntenZij"])?'<font color="green">'.$row["PuntenWij"].'</font>':'<font color="red">'.$row["PuntenWij"].'</font>').'</b></td>
                    <td>'.$row["Zij1"].' &<br>'.$row["Zij2"].'<br><b>'.(($row["PuntenZij"]>$row["PuntenWij"])?'<font color="green">'.$row["PuntenZij"].'</font>':'<font color="red">'.$row["PuntenZij"].'</font>').'</b></td>
                    <td><b>'.$row["ELO_na"].'</b><br><br>'.(($row["ELO_na"]>$row["ELO_voor"])?'<font color="green">+ '.($row["ELO_na"]-$row["ELO_voor"]).'</font>':'<font color="red">- '.($row["ELO_voor"]-$row["ELO_na"]).'</font>').'</td>
               </tr>
               ';

             $i = $i - 1;


          }
          ?>
     </table>
</div>
</div>
  <p class="footer text-center text-muted">
    © 2018 - <?php
echo date("Y"); ?> <a href="mailto:breinink@gmail.com">brein inc.</a> &nbsp;
  </p>
  </div>
</body>
</html>


<script>
var Alles = [];
var Alles2 = [];
var Gehaald = [];
var Gespeeld = [];
var Gewonnen = [];
var Verloren = [];
var Rolling = [];
var period = 10;
var gemmie = 1200;
var gem_gesp = 50;
var gem_geh = 71;

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
var plotLineColor = isDarkMode ? '#aaa' : 'black';

var handen = <?php
echo json_encode($handen, JSON_FORCE_OBJECT); ?>;
Data = <?php
echo json_encode($rows); ?>;
Data2 = <?php
echo json_encode($rows2); ?>;
Data3 = <?php
echo json_encode($rows3); ?>;
Win = <?php
echo json_encode($win); ?>;
var Naam = 'test';
Naam = <?php
echo json_encode($naam); ?>;
var total = 0;

for(var i = 0; i < Data.length; i++) {
    total += Data[i];
    Alles[i] = { x: i, y: Data[i] };
    if(i<period) {
      Rolling[i] = { x: i, y: null };
      Gespeeld[i] = { x: i, y: null };
      Gehaald[i] = { x: i, y: null };
    } else {
      gemmie = (Data[i]+Data[i-1]+Data[i-2]+Data[i-3]+Data[i-4]+Data[i-5]+Data[i-6]+Data[i-7]+Data[i-8]+Data[i-9]) / 10;
      gem_gesp = (Data2[i]+Data2[i-1]+Data2[i-2]+Data2[i-3]+Data2[i-4]+Data2[i-5]+Data2[i-6]+Data2[i-7]+Data2[i-8]+Data2[i-9]) / 10;
      gem_geh = (Data3[i]+Data3[i-1]+Data3[i-2]+Data3[i-3]+Data3[i-4]+Data3[i-5]+Data3[i-6]+Data3[i-7]+Data3[i-8]+Data3[i-9]) / 10;
      Rolling[i] = { x: i, y: gemmie };
      Gespeeld[i] = { x: i, y: gem_gesp };
      Gehaald[i] = { x: i, y: gem_geh };
    }

}
var avg = total / Data.length;
var total2 = 0;
for(var w = 0; w < Data2.length; w++) {
    total2 += Data2[w];
    if (w > 0) {
      var f = w - 1;
      Alles2[f] = { x: f, y: Data2[f]};
    }
    if (Win[w] === 1) {
      Gewonnen.push({ x: w, y: Data2[w]});
    } else if  (Win[w] === 0){
      Verloren.push({ x: w, y: Data2[w]});
    }
}
var avg2 = total2 / Data2.length;

var Extra =  Math.round(Data.length/20) + 1;
var Poli =  Math.floor(Data.length/30) + 2;


Highcharts.chart('container', {
    chart: {
    backgroundColor: null,
    type: 'scatter',
zoomType: 'x',
events: {
    load: function() {
        this.series[1].update({
            enableMouseTracking: false
        })
    }
}


  },

    title: {
        text: 'ELO-score'
    },
    credits: {
       enabled: false
     },
    legend: {
        enabled: false
    },
    tooltip: {
    headerFormat: '<b>{series.name}</b><br>',
    formatter: function () {
        return 'De ELO-score na pot <b>' + this.x +
            '</b> is <b>' + Math.round(this.y) + '</b>';
    }
},

    subtitle: {
        text: ''
    },

    yAxis: {
    plotLines:[{
      value: 1200,
      color: plotLineColor,
      width:2,
      zIndex:1,
      label:{text:'Landelijk gemiddelde', style:{color: plotLineColor}}
    }
    ],
        title: {
            text: 'ELO'
        }
    },
    xAxis: {
      gridLineWidth: 1,
      allowDecimals: false,
        title: {
            text: 'Pot'
        }
    },

    plotOptions: {
      scatter:{
    lineWidth:3
},
        series: {
            label: {
                connectorAllowed: false
            },
            pointStart: 0
        }
    },

    series: [{
      regression: false,
      regressionSettings: {
        name: 'Trend',
        type: 'polynomial',
        order: Poli,
        decimalPlaces: 0,
        color: '#EB984E',
        dashStyle: 'dash',
        extrapolate: Extra
      },
        name: Naam,
        data: Alles,
        marker: {
           enabled: false
       }
    }, {
      name: 'Gem. over 10 potten',
      data: Rolling,
      color: '#EB984E',
      marker: {
         enabled: false
     }
    }],

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
Highcharts.chart('container2', {
    chart: {
    backgroundColor: null,
    type: 'scatter',
zoomType: 'x',
events: {
}


  },

    title: {
        text: 'Gespeeld vs Gehaald'
    },
    credits: {
       enabled: false
     },
    legend: {
        enabled: false
    },
    tooltip: {
    headerFormat: '<b>{series.name}</b><br>',
    formatter: function () {
        return 'Van pot ' + Math.round(this.x - 9) + ' t/m pot ' + this.x + ' als team <b>' + this.y.toFixed(1) + '%</b> ' + this.series.name.toLowerCase();
    }
},

    subtitle: {
        text: ''
    },

    yAxis: {
    plotLines:[{
      value: 70,
      color: '#7CB5EC',
      width:2,
      zIndex:1,
      label:{text:'Landelijk gemiddelde'}
    },{
      value: 50,
      color: '#EB984E',
      width:2,
      zIndex:1,
      label:{text:'Landelijk gemiddelde'}
    }
    ],
        title: {
            text: '%'
        }
    },
    xAxis: {
      gridLineWidth: 1,
      allowDecimals: false,
        title: {
            text: 'Pot'
        }
    },

    plotOptions: {
      scatter:{
    lineWidth:3
},
        series: {
            label: {
                connectorAllowed: false
            },
            pointStart: 0
        }
    },

    series: [{
      regression: false,
      regressionSettings: {
        name: 'Trend',
        type: 'polynomial',
        order: Poli,
        decimalPlaces: 0,
        color: '#EB984E',
        dashStyle: 'dash',
        extrapolate: Extra
      },
        name: 'Gehaald',
        data: Gespeeld,
        marker: {
           enabled: false
       }
    }, {
      name: 'Gespeeld',
      data: Gehaald,
      color: '#EB984E',
      marker: {
         enabled: false
     }
    }],

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
function toggleDark(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';localStorage.setItem('boernel-theme',isDark?'light':'dark');window.location.reload();}
document.addEventListener('DOMContentLoaded',function(){var isDark=document.documentElement.getAttribute('data-theme')==='dark';var icon=document.getElementById('darkIcon');if(icon)icon.className=isDark?'fas fa-sun':'fas fa-moon';});
</script>
