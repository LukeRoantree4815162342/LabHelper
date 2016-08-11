<html>
<head>
  <?php // ?>
   <title>Simplified Data Analysis</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['X', 'Y']						
          <?php
	  for($i=0; $i<10; $i++){
        	echo ',[' . $_POST["x".$i] . ',' . $_POST["y".$i] . ']';
    	  }
	  ?>
        ]);

        var options = {
          title: 'X vs.Y',
          hAxis: {title: 'X'}, 
          vAxis: {title: 'Y'}, 
          legend: 'none',
          trendlines: {
    		0: {
     		type: 'linear',
      		color: 'black',
      		lineWidth: 3,
      		opacity: 0.8,
      		showR2: true,
      		visibleInLegend: true
    		}
  	}
        };
        var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
</head>

<body>

<h1>Simplified version of my Lab Data Analysis Project, written in PHP</h1>

<br><br><br>

<form method="post">
<table style="width:600px;">
<tr>
<td>X</td><td>Delta X</td><td>Y</td><td>Delta Y</td>
</tr>
<?php
for ($i=0; $i<10; $i++){	//note: here the '10' is used because I have 10 data points, for a more generic program that would be a variable containing no. data points
  ?>
  <tr>
  <td><input type="text" name="x<?=$i?>"></td>
  <td><input type="text" name="deltax<?=$i?>"></td>
  <td><input type="text" name="y<?=$i?>"></td>
  <td><input type="text" name="deltay<?=$i?>"></td>
  </tr>
  <?php
}
?>
</table>
<input type="submit" value="submit">
</form>

<?php
  if (array_key_exists("x0", $_POST) && array_key_exists("y9", $_POST)){	//just checks first and last values are entered, in case 'submit' is pressed early
    
    $sumXvalues = 0;
    $sumYvalues = 0;
    for($i=0; $i<10; $i++){	
        $sumXvalues+=$_POST["x".$i];
        $sumYvalues+=$_POST["y".$i];
    }
    $Xmean = ($sumXvalues*1.0/10.0);
    $Ymean = ($sumYvalues*1.0/10.0);
    $topline=0;
    $bottomline=0;
    for($i=0; $i<10; $i++){
    $topline+=($_POST["x".$i]-$Xmean)*($_POST["y".$i]-$Ymean);
    $bottomline+=($_POST["x".$i]-$Xmean)*($_POST["x".$i]-$Xmean);	//here I don't include error messages for extreme cases, e.g. where a division by zero will occur.
    }
    $gradient = $topline*1.0 / $bottomline*1.0;
    $intercept = $Ymean - $gradient * $Xmean;
    $sigmaSquaredY = 0;
    $sumXSquared = 0;
    for($i=0; $i<10; $i++){
        $sigmaSquaredY+=pow(($_POST["y".$i] -$gradient*$_POST["x".$i]-$intercept),2);
        $sumXSquared+=pow(($_POST["x".$i]),2);
    }
    $sigmaSquaredY = $sigmaSquaredY*1.0/8.0; //because the for loop doesn't quite calculate sigmaSquaredY, it needs to be divided by the no. degrees of freedom (N-2 here)
    $sigmaSquaredM = (10.0*$sigmaSquaredY)/((10.0*$sumXSquared)+pow($sumXvalues,2));
    $sigmaSquaredC = ($sigmaSquaredM*1.0/10.0)*($sumXSquared);
    $sigmaM = sqrt($sigmaSquaredM);
    $sigmaC = sqrt($sigmaSquaredC);
  }
?>
Your gradient for best fit is: <?=$gradient?> +- <?=$sigmaM?><br>
Your intercept for best fit is: <?=$intercept?> +- <?=$sigmaC?><br>
<div id="chart_div" style="width: 900px; height: 500px;"></div>

</body>
</html>