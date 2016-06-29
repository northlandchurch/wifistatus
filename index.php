<?php
error_reporting(0);
date_default_timezone_set('America/New_York');
include 'meekrodb.2.3.class.php';
include 'db-config.php';

function writedata($probe, $data) {

  $intervals = (isset($_GET['intervals'])) ? $_GET['intervals'] : 'raw';
  $fromdt = (isset($_GET['fromdt'])) ? urldecode($_GET['fromdt']) : date('Y-m-d H:i:s', strtotime('-1 hour'));
  $todt = (isset($_GET['todt'])) ? urldecode($_GET['todt']) : date('Y-m-d H:i:s');

  switch ($intervals) {
    case 'avg':
      $records = DB::Query("select avg(%l_data) as %l_data, convert((min(datetime) div 500)*500 + 230, datetime) as time from wifidata where  label = %s_probe and datetime between %t_fromdt and %t_todt group by datetime div 500",
        array(
          'data' => $data,
          'fromdt' => $fromdt,
          'todt' => $todt,
          'probe' => $probe
        )
      );
      break;

    default:
      $records = DB::Query("select %l_data from wifidata where label = %s_probe and datetime between %t_fromdt and %t_todt order by datetime",
        array(
          'data' => $data,
          'fromdt' => $fromdt,
          'todt' => $todt,
          'probe' => $probe
        )
      );
    break;
  }

  $i = 1;
  foreach ($records as $row) {
    if ($i < count($records)) {
      echo $row[$data] . ', ';
      $i++;
    } else {
      echo $row[$data];
    }
  }

}

function writedt($probe) {

  $fromdt = urldecode($_GET['fromdt']);
  $todt = urldecode($_GET['todt']);

  if (isset($_GET['fromdt'])) {
    $datetimes = DB::Query("select datetime from wifidata where label = %s_probe and datetime between %t_fromdt and %t_todt order by datetime",
      array(
        'fromdt' => $fromdt,
        'todt' => $todt,
        'probe' => $probe
      )
    );
  } else {
    $datetimes = DB::Query('select datetime from wifidata where date_sub(NOW(), INTERVAL 1 HOUR) < datetime and label = %s order by datetime', $probe);
  }

  $i = 1;
  foreach ($datetimes as $row) {
    $date = new DateTime($row['datetime']);
    if ($i < count($datetimes)) {
      echo '\'' . $date->format('m-d H:i') . '\', ';
      $i++;
    } else {
      echo '\'' . $date->format('m-d H:i') . '\'';
    }
  }
}
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Northland Wifi Status Dashboard</title>

    <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" type="text/css" href="https://www.highcharts.com/samples/static/highslide.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://www.highcharts.com/samples/static/highslide-full.min.js"></script>
    <script src="https://www.highcharts.com/samples/static/highslide.config.js" charset="utf-8"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="js/moment.js"></script>
    <script src="js/bootstrap-datetimepicker.min.js"></script>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <form class="form-inline text-center" action="/" method="get">
            <p>
              <div class="form-group">
                  <div class='input-group date' id='startdatetime'>
                      <span class="input-group-addon" id="startlabel">From:</span>
                      <input type='text' class="form-control" name="fromdt"/>
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>
                  <script type="text/javascript">
                    $(function () {
                      $('#startdatetime').datetimepicker();
                    });
                  </script>

              </div>

              <div class="form-group">
                  <div class='input-group date' id='enddatetime'>
                    <span class="input-group-addon" id="endlabel">To:</span>
                      <input type='text' class="form-control" name="todt"/>
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>
                  <script type="text/javascript">
                    $(function () {
                      $('#enddatetime').datetimepicker();
                    });
                  </script>

              </div>

            <div class="form-group">
              <select class="form-control" name="intervals">
                <option value="raw">1 minute intervals</option>
                <option value="avg">5 minute avg intervals</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="probe">
                <option value="greenroom">Green Room</option>
              </select>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
          </p>
          </form>
        </div>

      </div>
      <div class="row">
        <div class="col-lg-12">

    <div id="container1" style="min-width: 310px; height: 360px; margin: 0 auto"></div>
    <br>
    <div id="container2" style="min-width: 310px; height: 360px; margin: 0 auto"></div>

    <script type="text/javascript">
    $(function () {


      $('#container1').highcharts({
          title: {
            text: 'Green Room "Team" Network Response Times',
            x: -20 //center
          },
          subtitle: {
            text: 'The Team wifi network bandwidth is not restricted.',
            x: -20
          },
          xAxis: {
            categories: [<?php @writedt('Green Room - Team'); ?>]
          },
          yAxis: {
            title: {
              text: 'Response Times in Miliseconds'
            },
            plotLines: [{
              value: 0,
              width: 1,
              color: '#808080'
            }]
          },
          tooltip: {
            valueSuffix: ' ms'
          },
          credits: {
            enabled: false
          },
          legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
          },
          series: [{
            name: 'Northland',
            data: [<?php writedata('Green Room - Team', 'northland'); ?>]
          }, {
            name: 'PCO',
            data: [<?php writedata('Green Room - Team', 'pco'); ?>]
          }, {
            name: 'Slack',
            data: [<?php writedata('Green Room - Team', 'slack'); ?>]
          }, {
            name: 'Google',
            data: [<?php writedata('Green Room - Team', 'google'); ?>]
          }, {
            name: 'Core Switch',
            data: [<?php writedata('Green Room - Team', 'coreswitch'); ?>]
          }]
        });
      $('#container2').highcharts({
            title: {
              text: 'Green Room "Northland" Network Response Times',
              x: -20 //center
            },
            subtitle: {
              text: 'The Northland wifi bandwidth is restricted during the Sunday AM services.',
              x: -20
            },
            xAxis: {
              categories: [<?php @writedt('Green Room - Northland'); ?>]
            },
            yAxis: {
              title: {
                text: 'Response Times in Miliseconds'
              },
              plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
              }]
            },
            tooltip: {
              valueSuffix: ' ms'
            },
            credits: {
              enabled: false
            },
            legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle',
              borderWidth: 0
            },
            series: [{
              name: 'Northland',
              data: [<?php writedata('Green Room - Northland', 'northland'); ?>]
            }, {
              name: 'PCO',
              data: [<?php writedata('Green Room - Northland', 'pco'); ?>]
            }, {
              name: 'Slack',
              data: [<?php writedata('Green Room - Northland', 'slack'); ?>]
            }, {
              name: 'Google',
              data: [<?php writedata('Green Room - Northland', 'google'); ?>]
            }, {
              name: 'Core Switch',
              data: [<?php writedata('Green Room - Northland', 'coreswitch'); ?>]
            }]
          });
    });
    /**
     * Sand-Signika theme for Highcharts JS
     * @author Torstein Honsi
     */

    // Load the fonts
    Highcharts.createElement('link', {
       href: 'https://fonts.googleapis.com/css?family=Signika:400,700',
       rel: 'stylesheet',
       type: 'text/css'
    }, null, document.getElementsByTagName('head')[0]);

    // Add the background image to the container
    Highcharts.wrap(Highcharts.Chart.prototype, 'getContainer', function (proceed) {
       proceed.call(this);
       this.container.style.background = 'url(http://www.highcharts.com/samples/graphics/sand.png)';
    });


    Highcharts.theme = {
      colors: ["#f45b5b", "#8085e9", "#8d4654", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
        "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
        chart: {
          backgroundColor: null,
          style: {
            fontFamily: "Signika, serif"
          }
        },
      title: {
        style: {
         color: 'black',
         fontSize: '16px',
         fontWeight: 'bold'
        }
       },
       subtitle: {
          style: {
             color: 'black'
          }
       },
       tooltip: {
          borderWidth: 0
       },
       legend: {
          itemStyle: {
             fontWeight: 'bold',
             fontSize: '13px'
          }
       },
       xAxis: {
          labels: {
             style: {
                color: '#6e6e70'
             }
          }
       },
       yAxis: {
          labels: {
             style: {
                color: '#6e6e70'
             }
          }
       },
       plotOptions: {
          series: {
             shadow: true
          },
          candlestick: {
             lineColor: '#404048'
          },
          map: {
             shadow: false
          }
       },

       // Highstock specific
       navigator: {
          xAxis: {
             gridLineColor: '#D0D0D8'
          }
       },
       rangeSelector: {
          buttonTheme: {
             fill: 'white',
             stroke: '#C0C0C8',
             'stroke-width': 1,
             states: {
                select: {
                   fill: '#D0D0D8'
                }
             }
          }
       },
       scrollbar: {
          trackBorderColor: '#C0C0C8'
       },

       // General
       background2: '#E0E0E8'

    };

      // Apply the theme
      Highcharts.setOptions(Highcharts.theme);
    </script>

  </div>

  </div>
</div>
  </body>
</html>
