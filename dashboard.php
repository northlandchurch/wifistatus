<?php
include 'meekrodb.2.3.class.php';
include 'db-config.php';

function writedt($probe) {
  $datetimes = DB::Query('select datetime from wifidata where date_sub(NOW(), INTERVAL 1 HOUR) > datetime and label = %s order by datetime', $probe);
  $end = end($datetimes);
  foreach ($datetimes as $row) {
    if ($end != $row) {
      echo '\'' . $row['datetime'] . '\', ';
    } else {
      echo '\'' . $row['datetime'] . '\'';
    }
  }
}

function writenorthland($probe) {
  $northlandresponses = DB::Query('select northland from wifidata where date_sub(NOW(), INTERVAL 1 HOUR) > datetime and label = %s order by datetime', $probe);
  $end = end($northlandresponses);
  foreach ($northlandresponses as $row) {
    if ($end != $row) {
      echo $row['northland'] . ', ';
    } else {
      echo $row['northland'];
    }
  }
}

function writepco($probe) {
  $pcoresponses = DB::Query('select pco from wifidata where date_sub(NOW(), INTERVAL 1 HOUR) > datetime and label = %s order by datetime', $probe);
  $end = end($pcoresponses);
  foreach ($pcoresponses as $row) {
    if ($end != $row) {
      echo $row['pco'] . ', ';
    } else {
      echo $row['pco'];
    }
  }
}

function writeslack($probe) {
  $slackresponses = DB::Query('select slack from wifidata where date_sub(NOW(), INTERVAL 1 HOUR) > datetime and label = %s order by datetime', $probe);
  $end = end($slackresponses);
  foreach ($slackresponses as $row) {
    if ($end != $row) {
      echo $row['slack'] . ', ';
    } else {
      echo $row['slack'];
    }
  }
}

function writegoogle($probe) {
  $googleresponses = DB::Query('select google from wifidata where date_sub(NOW(), INTERVAL 1 HOUR) > datetime and label = %s order by datetime', $probe);
  $end = end($googleresponses);
  foreach ($googleresponses as $row) {
    if ($end != $row) {
      echo $row['google'] . ', ';
    } else {
      echo $row['google'];
    }
  }
}

function writecoreswitch($probe) {
  $coreswitchresponses = DB::Query('select coreswitch from wifidata where date_sub(NOW(), INTERVAL 1 HOUR) > datetime and label = %s order by datetime', $probe);
  $end = end($coreswitchresponses);
  foreach ($coreswitchresponses as $row) {
    if ($end != $row) {
      echo $row['coreswitch'] . ', ';
    } else {
      echo $row['coreswitch'];
    }
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Wifi Status</title>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>
    <style>
    body {
      font-family: 'Cabin', sans-serif;
    }
  </style>
  </head>
  <body>
    <div class="ct-chart ct-perfect-fifth"></div>
    <script type="text/javascript">
      var data = {
        labels: [<?php @writedt('Green Room - Team'); ?>],
        series: [
          [<?php @writenorthland('Green Room - Team'); ?>],
          [<?php @writepco('Green Room - Team'); ?>],
          [<?php @writeslack('Green Room - Team'); ?>],
          [<?php @writegoogle('Green Room - Team'); ?>]
        ]
      };

      // We are setting a few options for our chart and override the defaults
      var options = {
        // Don't draw the line chart points
        showPoint: true,
        // Disable line smoothing
        lineSmooth: true,
        // X-Axis specific configuration
        axisX: {
          // We can disable the grid for this axis
          showGrid: true,
          // and also don't show the label
          showLabel: true
        },
        // Y-Axis specific configuration
        axisY: {
          showLabel: true,
          // Lets offset the chart a bit from the labels
          offset: 60,
          // The label interpolation function enables you to modify the values
          // used for the labels on each axis. Here we are converting the
          // values into million pound.
          labelInterpolationFnc: function(value) {
            return value + ' ms';
          }
        }
      };

      // All you need to do is pass your configuration as third parameter to the chart function
      new Chartist.Line('.ct-chart', data, options);
    </script>
  </body>
</html>
