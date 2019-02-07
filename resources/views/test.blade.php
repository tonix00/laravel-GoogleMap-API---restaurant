<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>

    <title>Restaurants in Cebu</title>
    
    <link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16" />

   
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['days', 'visitors'],
          ['1',  0],
          ['2',  0],
          ['3',  0],
          ['4',  2],
          ['5',  0],
          ['6',  0],
          ['7',  0],
          ['8',  0],
          ['9',  3],
          ['10',  2],
          ['11',  0],
          ['12',  0],
          ['13',  0],
          ['14',  0],
          ['15',  1],
          ['16',  0],
          ['17',  0],
          ['18',  0],
          ['19',  0],
          ['20',  0],
          ['21',  0],
          ['22',  0],
          ['23',  0],
          ['24',  0],
          ['25',  0],
          ['26',  0],
          ['27',  0],
          ['28',  1]
        ]);

        var options = {
          title: 'Visitors for the Month of Febuary',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
</head>
<body scroll="no" style="overflow: hidden">
   
<div id="curve_chart" style="width: 700px; height: 200px"></div>
</body>
</html>