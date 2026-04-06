<?php
require_once __DIR__ . '/db.php';
$connect = dbConnect();
$query ="SELECT Datum, GemPuntenTotaal FROM perspelerperdatum where Naam = 'Bernard'";
$result = mysqli_query($connect, $query);
$scores = array();
$datum = array();
$index = 0;
$data = array();


  while($row = mysqli_fetch_array($result))
  {
    //array_push($data, $row[]);
    $scores[] = $row["GemPuntenTotaal"];
    $datum[] = $row["Datum"];
    $data[$index][] = $row["Datum"];
    $data[$index][] = $row["GemPuntenTotaal"];
    $index++;




  }


?>


<!DOCTYPE html>
<html>
<head>
	<title>HighChart</title>
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/series-label.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>

</head>
<body>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<script type="text/javascript">

    var data_datum = [<?php
echo join($datum, ',') ?>]
    var data_score = [<?php
echo join($scores, ',') ?>]
    var data = [<?php
echo join($data, ',') ?>]


    Highcharts.chart('container', {
      chart: {
        type: 'spline'
      },
      title: {
        text: 'Scoresoverzicht'
      },
      subtitle: {
        text: 'Andere tekst'
      },
      xAxis: {
        type: 'datetime',
        dateTimeLabelFormats: { // don't display the dummy year
          month: '%e. %b',
          year: '%b'
        },
        title: {
          text: 'Date'
        }
      },
      yAxis: {
        title: {
          text: 'Score'
        },
        min: 0
      },
      tooltip: {
        headerFormat: '<b>{series.name}</b><br>',
        pointFormat: '{point.x:%e. %b}: {point.y:.2f} m'
      },

      plotOptions: {
        spline: {
          marker: {
            enabled: true
          }
        }
      },


      colors: ['#6CF', '#39F', '#06C', '#036', '#000'],

      // Define the data points. All series have a dummy year
      // of 1970/71 in order to be compared on the same x axis. Note
      // that in JavaScript, months start at 0 for January, 1 for February etc.
      series: [{
        name: "Bernard",
        data: [data_datum, data_score]
      }]
    });
    </script>



</body>
</html>
