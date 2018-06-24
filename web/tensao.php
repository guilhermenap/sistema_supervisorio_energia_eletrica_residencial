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
	$separa2=explode(',',$separa1[1]);//separa tensão por circuito Va vb vc
		for($i=0;$i<3;$i++)
		{
				$horario_tensao[strtotime($row_consumo['hora'])-60*60*3][$i]=$separa2[$i];//array de tensao
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
$maior=array(0,0,0);
$menor=array(1000,1000,1000);
while($inicio<$final)
{
$fim=$inicio+120;
	
	foreach($horario_tensao as $chave=>$circuito)
	{
		if($chave>$inicio and $chave<=$fim)
		{
			
			foreach($circuito as $chave2=>$valor)
			{
				if($valor<0)
				{
					$valor=0.00;
				}
				
				$cont[$fim][$chave2]=$cont[$fim][$chave2]+1;
				$graf_fim[$fim][$chave2]=$graf_fim[$fim][$chave2]+$valor;
				
				if($valor>$maior[$chave2])
				{
					$maior[$chave2]=$valor;
				}
				if($valor<$menor[$chave2])
				{
					$menor[$chave2]=$valor;
				}
			}
		}
	}
	
	
	
	for($c=0;$c<3;$c++)
	{
		if($cont[$fim][$c]==0)
		{
			$cont[$fim][$c]=1;
		}
	$graf_fim[$fim][$c]=number_format($graf_fim[$fim][$c]/$cont[$fim][$c], 2, '.', '');	
		if($graf_fim[$fim][$c]>$maior)
		{
			$maior=$graf_fim[$fim][$c];

		}
	}

	for($a=0;$a<3;$a++)
	{
			if($graf_fim[$fim][$a]!='0.00')
			{
				if($tensao_graf[$a]==false)
				{
					//$corrente_graf[$a]="[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][$a]."]";
					$tensao_graf[$a]="[{v: [".date('H',$fim).",".date('i',$fim)."], f: '".date('H:i',$fim)."'},".$graf_fim[$fim][$a]."]";
				}
				else
				{
					//$corrente_graf[$a]=$corrente_graf[$a].",[[".date('H',$fim).",".date('i',$fim)."],".$graf_fim[$fim][$a]."]";
					$tensao_graf[$a]=$tensao_graf[$a].",[{v: [".date('H',$fim).",".date('i',$fim)."], f: '".date('H:i',$fim)."'},".$graf_fim[$fim][$a]."]";
				}
			}
	}


	$inicio=$inicio+120;
}
?>
<?php include("cima.php"); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php for($b=0;$b<3;$b++) { ?>	
<script type="text/javascript">
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {

      var data = new google.visualization.DataTable();
      data.addColumn('timeofday', 'Tempo');
      data.addColumn('number', 'Tensão');

      data.addRows([
        <?php echo $tensao_graf[$b];?>
      ]);

      var options = {
		   title: 'Histórico Tensão Circuito <?php echo $b+1;?>',
        hAxis: {
          title: 'Hora do dia'
        },
        vAxis: {
          title: 'Tensão [V]'
        }
      };

      var chart = new google.visualization.LineChart(
        document.getElementById('graf<?php echo $b+1;?>'));

      chart.draw(data, options);
    }
    </script>
    <?php } ?>
 
      <body>
  <table width="80%" border="0" align="center">
        <tbody>
          <tr>
            <td align="center"><a href="index.php"><br>
            Voltar<br>
              <br>
            </a></td>
          </tr>
          <tr>
            <td height="30" align="center" ><form name="form1" method="get" action="tensao.php">
              Data:
              <input type="date" name="data" id="data" value="<?php echo $_GET['data'];?>">
              <input type="submit"name="button" id="button" value="Enviar" >
            </form></td>
          </tr>
          <tr>
            <td height="30" align="center" class="borda" <?php echo $barra;?>><strong>Histórico de Tensão</strong>: <strong>
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
            <td class="borda"><p>Variação de tensões:<br>
              Va - <?php echo $menor[0].' ~ '.$maior[0];?><br>
              Vb - <?php echo $menor[1].' ~ '.$maior[1];?><br>
              Vc -<?php echo $menor[2].' ~ '.$maior[2];?>
              <br>
            </p></td>
          </tr>
          <tr>
            <td height="30" align="center" class="borda" <?php echo $barra;?>><strong>Gráfico de Tensão por Circuito</strong></td>
          </tr>
          <tr>
            <td class="borda"><div id="graf1" style="width: 100 %; height: 300px"></div></td>
          </tr>
          <tr>
            <td class="borda"><div id="graf2" style="width: 100 %; height: 300px"></div></td>
          </tr>
          <tr>
            <td class="borda"><div id="graf3" style="width: 100 %; height: 300px"></div></td>
          </tr>        </tbody>
      </table>
  </body>
  
</html>
