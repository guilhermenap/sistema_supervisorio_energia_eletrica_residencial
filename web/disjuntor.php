<?php require_once('classes.php');
autorizado('1','level');
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
mysqli_select_db($servidor,$database_servidor);
$query_consumo = "SELECT * FROM consumo ORDER BY id ASC";
$consumo = mysqli_query($servidor,$query_consumo);
$row_consumo = mysqli_fetch_assoc($consumo);
$totalRows_consumo = mysqli_num_rows($consumo);
do{
	$separa1=explode('/',$row_consumo['consumo']);
	$separa2=explode(',',$separa1[2]);//separa tensão por circuito Va vb vc
		for($i=0;$i<5;$i++)
		{
				$horario_tensao[strtotime($row_consumo['hora'])][$i]=$separa2[$i];//array de tensao
		}
	 } while ($row_consumo = mysqli_fetch_assoc($consumo));
///----
$inicio=strtotime(date('Y-m-d 00:00'));
$maior=0;
while($inicio<strtotime(date('Y-m-d 24:00')))
{
$fim=$inicio+300;
	
	foreach($horario_tensao as $chave=>$circuito)
	{
		if($chave>$inicio and $chave<=$fim)
		{
			
			foreach($circuito as $chave2=>$valor)
			{

				$cont[$fim][$chave2]=$cont[$fim][$chave2]+1;
				$graf_fim[$fim][$chave2]=$graf_fim[$fim][$chave2]+$valor;
			}
		}
	}
	
	
	
	for($c=0;$c<5;$c++)
	{
	$graf_fim[$fim][$c]=number_format($graf_fim[$fim][$c]/$cont[$fim][$c], 2, '.', '');	
		if($graf_fim[$fim][$c]>$maior)
		{
			$maior=$graf_fim[$fim][$c];

		}
	}

	
	
	$tensao_graf[0]=$tensao_graf[0].",[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][0]."]";
	$tensao_graf[1]=$tensao_graf[1].",[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][1]."]";
	$tensao_graf[2]=$tensao_graf[2].",[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][2]."]";
	$tensao_graf[3]=$tensao_graf[3].",[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][3]."]";
	$tensao_graf[4]=$tensao_graf[4].",[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][4]."]";

	$inicio=$inicio+300;
}
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Hora', 'V1']
		  <?php echo $tensao_graf[0];?>
        ]);
		

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.charts.Bar(document.getElementById('graf1'));

        chart.draw(data, options);
      }
    </script>
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Hora', 'V2']
		  <?php echo $tensao_graf[1];?>
        ]);
		

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.charts.Bar(document.getElementById('graf2'));

        chart.draw(data, options);
      }
    </script>
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Hora', 'V3']
		  <?php echo $tensao_graf[2];?>
        ]);
		

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.charts.Bar(document.getElementById('graf3'));

        chart.draw(data, options);
      }
    </script>
    
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Hora', 'V4']
		  <?php echo $tensao_graf[3];?>
        ]);
		

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.charts.Bar(document.getElementById('graf4'));

        chart.draw(data, options);
      }
    </script>
    
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Hora', 'V5']
		  <?php echo $tensao_graf[4];?>
        ]);
		

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.charts.Bar(document.getElementById('graf5'));

        chart.draw(data, options);
      }
    </script>
    
 
      <body>
    <div id="graf1" style="width: 90 %; height: 300px"></div>
    <div id="graf2" style="width: 90 %; height: 300px"></div>
    <div id="graf3" style="width: 90 %; height: 300px"></div>
    
    <div id="graf4" style="width: 90 %; height: 300px"></div>
    
    <div id="graf5" style="width: 90 %; height: 300px"></div>
  </body>
  
</html>
