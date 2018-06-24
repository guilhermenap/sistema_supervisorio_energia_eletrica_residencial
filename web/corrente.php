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
$consumo=addslashes($_GET['consumo']);
if($consumo)
{
mysqli_select_db($servidor, $database_servidor);
mysqli_query($servidor,"INSERT INTO `consumo` (`id`, `hora`, `consumo`, `desativado`) VALUES (NULL, CURRENT_TIMESTAMP, '$consumo', '');");
}

mysqli_select_db($servidor,$database_servidor);
$query_consumo = "SELECT * FROM consumo ORDER BY id ASC";
$consumo = mysqli_query($servidor,$query_consumo);
$row_consumo = mysqli_fetch_assoc($consumo);
$totalRows_consumo = mysqli_num_rows($consumo);
do{
	$separa1=explode('/',$row_consumo['consumo']);
	$separa2=explode(',',$separa1[0]);//separa corrente por cirucuito
	$separa3=explode(',',$separa1[1]);//separa tensão por circuito
		for($i=0;$i<5;$i++)
		{
		$horario_corrente[strtotime($row_consumo['hora'])-60*60*3][$i]=$separa2[$i];//array de corrente
		}
	 } while ($row_consumo = mysqli_fetch_assoc($consumo));
///----


if($_GET['data']==false)
{
	$inicio=strtotime(date('Y-m-d 00:01'));
	$final=strtotime(date('Y-m-d 23:58'));
}
else
{
	$inicio=strtotime(date(addslashes($_GET['data']).' 00:01'));
	$final=strtotime(date(addslashes($_GET['data']).' 23:58'));
}

while($inicio<$final)
{
$fim=$inicio+120;
	

	foreach($horario_corrente as $chave=>$circuito)
	{
		if($chave>$inicio and $chave<$fim)
		{
			
			foreach($circuito as $chave2=>$valor)
			{
					if($valor<0)
					{
					 $valor=0.00;	
					}
					$cont[$fim][$chave2]=$cont[$fim][$chave2]+1;
					$graf_fim[$fim][$chave2]=$graf_fim[$fim][$chave2]+$valor;
					
					if($maior[$chave2]<$valor)
					{
						$maior[$chave2]=$valor;
					}
				
			}
		}
	}
	
	for($c=0;$c<5;$c++)
	{
		if($cont[$fim][$c]==0)
		{
			$cont[$fim][$c]=1;
		}
		$graf_fim[$fim][$c]=number_format($graf_fim[$fim][$c]/$cont[$fim][$c], 2, '.', '');	
	}
	for($a=0;$a<5;$a++)
	{
			if($graf_fim[$fim][$a]!='0.00')
			{
				if($corrente_graf[$a]==false)
				{
					//$corrente_graf[$a]="[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][$a]."]";
					$corrente_graf[$a]="[{v: [".date('H',$fim).",".date('i',$fim)."], f: '".date('H:i',$fim)."'},".$graf_fim[$fim][$a]."]";
				}
				else
				{
					//$corrente_graf[$a]=$corrente_graf[$a].",[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][$a]."]";
					$corrente_graf[$a]=$corrente_graf[$a].",[{v: [".date('H',$fim).",".date('i',$fim)."], f: '".date('H:i',$fim)."'},".$graf_fim[$fim][$a]."]";
				}
			}
	}

	$inicio=$inicio+120;
}
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
<?php for($b=0;$b<5;$b++) { ?>	
	<script type="text/javascript">
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {

      var data = new google.visualization.DataTable();
      data.addColumn('timeofday', 'Time of Day');
      data.addColumn('number', 'Corrente');

      data.addRows([
        <?php echo $corrente_graf[$b];?>
      ]);

      var options = {
		  title: 'Histórico Corrente Circuito <?php echo $b+1;?>',
        hAxis: {
          title: 'Hora do dia'
        },
        vAxis: {
          title: 'Corrente [A]'
        }
      };

      var chart = new google.visualization.LineChart(
        document.getElementById('graf<?php echo $b+1;?>'));

      chart.draw(data, options);
    }
    </script>
    <?php } ?>

      <style type="text/css">
      .borda {	border: 1px solid #CCC;
}
      </style>
      <body>
      <?php include("cima.php"); ?>
      <table width="80%" border="0" align="center">
        <tbody>
          <tr>
            <td align="center"><a href="index.php"><br>
            Voltar<br>
              <br>
            </a></td>
          </tr>
          <tr>
            <td height="30" align="center" ><form name="form1" method="get" action="corrente.php">
              Data:
              <input type="date" name="data" id="data" value="<?php echo $_GET['data'];?>">
              <input type="submit"name="button" id="button" value="Enviar" >
            </form></td>
          </tr>
          <tr>
            <td height="30" align="center" class="borda" <?php echo $barra; ?>><strong>Histórico de Corrente: 
              <?php if($_GET['data']==false)
			{
				echo date('d/m/Y');
			} 
			else
			{ 
				echo date('d/m/Y',strtotime($_GET['data']));
				
			} ?>
            </strong></td>
          </tr>
          <tr>
            <td align="left" class="borda"><strong>Picos de correte por circuito</strong><br>
              <br>
              Circuito 1: <?php echo $maior[0]; ?> [A]<br>
              Circuito 2: <?php echo $maior[1]; ?> [A]<br>
              Circuito 3: <?php echo $maior[2]; ?> [A]<br>
              Circuito 4: <?php echo $maior[3]; ?> [A]<br>
              Circuito 5: <?php echo $maior[4]; ?> [A]<br></td>
          </tr>
          <tr>
            <td height="30" align="center" class="borda" <?php echo $barra; ?>><strong>Gráfico de Corrente por Circuito</strong></td>
          </tr>
          <tr >
            <td class="borda">            <div id="graf1" style="width: 100 %; height: 300px"></div></td>
          </tr>
          <tr>
            <td class="borda"><div id="graf2" style="width: 100 %; height: 300px"></div></td>
          </tr>
          <tr>
            <td class="borda"><div id="graf3" style="width: 100 %; height: 300px"></div></td>
          </tr>
          <tr>
            <td class="borda"><div id="graf4" style="width: 100 %; height: 300px"></div></td>
          </tr>
         <tr>
            <td class="borda"><div id="graf5" style="width: 100 %; height: 300px"></div></td>
          </tr>
        </tbody>
      </table>
      <p>&nbsp;</p>
    <p>&nbsp;</p>
  </body>
  
</html>
