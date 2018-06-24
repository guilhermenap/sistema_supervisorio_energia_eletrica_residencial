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
$query_config = "SELECT * FROM configuracao";
$config = mysqli_query($servidor,$query_config);
$row_config = mysqli_fetch_assoc($config);
$totalRows_config = mysqli_num_rows($config);

function tensao($array,$circuito)
{
	$quebra=explode(';',$array);
	$quebra2=explode(',',$quebra[0]);
	
	$tensao[$circuito][0]=$quebra2[0];
	$tensao[$circuito][1]=$quebra2[1];
	$tensao[$circuito][2]=$quebra2[2];
	return($tensao);
}
$tensao1=tensao($row_config['c1'],1);
$tensao2=tensao($row_config['c2'],2);
$tensao3=tensao($row_config['c3'],3);
$tensao4=tensao($row_config['c4'],4);
$tensao5=tensao($row_config['c5'],5);

$tensao=array_merge($tensao1,$tensao2,$tensao3,$tensao4,$tensao5);


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
	$inicio=strtotime(date('Y-m-d 00:00'));
	$final=strtotime(date('Y-m-d 24:00'));
}
else
{
	$inicio=strtotime(date(addslashes($_GET['data']).' 00:00'));
	$final=strtotime(date(addslashes($_GET['data']).' 24:00'));
}
$maior=array(0,0,0);
$menor=array(1000,1000,1000);
while($inicio<$final)
{
$fim=$inicio+3600;
	
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
				$graf_tensao[$fim][$chave2]=$graf_tensao[$fim][$chave2]+$valor;
				
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
	$graf_tensao[$fim][$c]=number_format($graf_tensao[$fim][$c]/$cont[$fim][$c], 2, '.', '');	
		if($graf_tensao[$fim][$c]>$maior)
		{
			$maior=$graf_tensao[$fim][$c];

		}
	}

	$inicio=$inicio+3600;
}
?>
<?php

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
	$inicio=strtotime(date('Y-m-d 00:00'));
	$final=strtotime(date('Y-m-d 24:00'));
}
else
{
	$inicio=strtotime(date(addslashes($_GET['data']).' 00:00'));
	$final=strtotime(date(addslashes($_GET['data']).' 24:00'));
}
$cont=array();

while($inicio<$final)
{
$fim=$inicio+3600;
	

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
		$cont_t=0;
		$tensao_circuito=0;
		foreach($tensao[$a] as $chave=>$valor)
		{
			if($valor)
			{
				$tensao_circuito=$tensao_circuito+$graf_tensao[$fim][$chave];
				$cont_t++;
			}
			
		}
		if($cont_t==2)
		{
			$tensao_circuito=($tensao_circuito/2)*1.73;	
		
		}
		
		
			if($corrente_graf[$a]==false)
			{
				//$corrente_graf[$a]="[{v: [".date('H',$fim).",".date('i',$fim)."], f: '".date('H:i',$fim)."'},".$graf_fim[$fim][$a]*$graf_tensao[$fim][0]."]";
				$corrente_graf[$a]="[{v: [".date('H',$fim).",".date('i',$fim)."], f: '".date('H:i',$fim)."'},".$graf_fim[$fim][$a]*$tensao_circuito."]";
			}
			else
			{
				//$corrente_graf[$a]=$corrente_graf[$a].",[{v: [".date('H',$fim).",".date('i',$fim)."], f: '".date('H:i',$fim)."'},".$graf_fim[$fim][$a]*$graf_tensao[$fim][0]."]";
				$corrente_graf[$a]=$corrente_graf[$a].",[{v: [".date('H',$fim).",".date('i',$fim)."], f: '".date('H:i',$fim)."'},".$graf_fim[$fim][$a]*$tensao_circuito."]";
			}
			$total_consumo[$a]=$total_consumo[$a]+($graf_fim[$fim][$a]*$tensao_circuito);
			
			if(intval(idate('H',$fim))<17 or intval(idate('H',$fim))>21)
			{
				$total_fp[$a]=$total_fp[$a]+($graf_fim[$fim][$a]*$tensao_circuito);	
			}
			if(intval(idate('H',$fim))>17 and intval(idate('H',$fim))<21)
			{
				$total_p[$a]=$total_p[$a]+($graf_fim[$fim][$a]*$tensao_circuito);
			}
			if(intval(idate('H',$fim))>16 and intval(idate('H',$fim))<18)
			{
				$total_i[$a]=$total_i[$a]+($graf_fim[$fim][$a]*$tensao_circuito);
			}
			if(intval(idate('H',$fim))>20 and intval(idate('H',$fim))<22)
			{
				$total_i[$a]=$total_i[$a]+($graf_fim[$fim][$a]*$tensao_circuito);
			}
	}

	$inicio=$inicio+3600;
}
//------------
$tarifa=explode(',',$row_config['tarifa']);
$imposto=1/(1-($tarifa[4]/100));

?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
<?php for($b=0;$b<5;$b++) { ?>	
	<script type="text/javascript">
google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {

      var data = new google.visualization.DataTable();
      data.addColumn('timeofday', 'Time of Day');
      data.addColumn('number', 'Potência [Wh]');

      data.addRows([
        <?php echo $corrente_graf[$b];?>
      ]);

      var options = {
        title: 'Histórico de Consumo <?php echo $b+1;?>',
        hAxis: {
          title: 'Hora do dia',
         
          viewWindow: {
            min: [00, 00, 0],
            max: [24, 00, 0]
          }
        },
        vAxis: {
          title: 'Consumo [Wh]',
		  viewWindow: {
            min: [0]
          }
        }
      };

      var chart = new google.visualization.ColumnChart(
        document.getElementById('graf<?php echo $b+1;?>'));

      chart.draw(data, options);
    }
    </script>
    <?php } ?>
<style>
.borda {
	border: 1px solid #CCC;
}
-->
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
            <td height="30" align="center" ><form name="form1" method="get" action="consumo.php">
            Data: 
           
            <input type="date" name="data" id="data" value="<?php echo $_GET['data'];?>">
            <input type="submit"name="button" id="button" value="Enviar" >
            </form></td>
          </tr>
          <tr>
            <td height="30" align="center" class="borda" <?php echo $barra; ?>><strong>Histórico de Consumo</strong>: 
			<?php if($_GET['data']==false)
			{
				echo date('d/m/Y');
			} 
			else
			{ 
				echo date('d/m/Y',strtotime($_GET['data']));
				
			} ?></td>
          </tr>
          <tr>
            <td align="center" class="borda"><strong><br>
            Consumo por circuito</strong><br>
              <table width="80%" border="0">
                <tbody>
                  <tr>
                    <td width="12%">&nbsp;</td>
                    <td width="11%" align="center" class="borda">[kWh]</td>
                    <td width="20%" align="center" class="borda">Custo Convencional [R$]</td>
                    <td width="12%" align="center" class="borda">Branca FP [R$]</td>
                    <td width="15%" align="center" class="borda">Branca Inter. [R$]</td>
                    <td width="15%" align="center" class="borda">Branca Ponta [R$]</td>
                    <td width="15%" align="center" class="borda">Branca Total [R$]</td>
                  </tr>
                  <tr>
                    <td class="borda">Circuito 1:</td>
                    <td align="center" class="borda"><?php echo number_format($total_consumo[0]*$imposto/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_consumo[0]*$tarifa[0]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_fp[0]*($tarifa[1]*$imposto))/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_i[0]*$tarifa[2]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_p[0]*$tarifa[3]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format((($total_fp[0]*$tarifa[1]*$imposto)+($total_i[0]*$tarifa[2]*$imposto)+($total_p[0]*$tarifa[3]*$imposto))/1000, 2, ',', ' '); ?></td>
                  </tr>
                  <tr>
                    <td class="borda">Circuito 2:</td>
                    <td align="center" class="borda"><?php echo number_format($total_consumo[1]/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_consumo[1]*$tarifa[0]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_fp[1]*($tarifa[1]*$imposto))/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_i[1]*$tarifa[2]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_p[1]*$tarifa[3]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format((($total_fp[1]*$tarifa[1]*$imposto)+($total_i[1]*$tarifa[2]*$imposto)+($total_p[1]*$tarifa[3]*$imposto))/1000, 2, ',', ' '); ?></td>
                  </tr>
                  <tr>
                    <td class="borda">Circuito 3:</td>
                    <td align="center" class="borda"><?php echo number_format($total_consumo[2]/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_consumo[2]*$tarifa[0]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_fp[2]*($tarifa[1]*$imposto))/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_i[2]*$tarifa[2]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_p[2]*$tarifa[3]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format((($total_fp[2]*$tarifa[1]*$imposto)+($total_i[2]*$tarifa[2]*$imposto)+($total_p[2]*$tarifa[3]*$imposto))/1000, 2, ',', ' '); ?></td>
                  </tr>
                  <tr>
                    <td class="borda">Circuito 4:</td>
                    <td align="center" class="borda"><?php echo number_format($total_consumo[3]/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_consumo[3]*$tarifa[0]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_fp[3]*($tarifa[1]*$imposto))/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_i[3]*$tarifa[2]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_p[3]*$tarifa[3]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format((($total_fp[3]*$tarifa[1]*$imposto)+($total_i[3]*$tarifa[2]*$imposto)+($total_p[3]*$tarifa[3]*$imposto))/1000, 2, ',', ' '); ?></td>
                  </tr>
                  <tr>
                    <td class="borda">Circuito 5:</td>
                    <td align="center" class="borda"><?php echo number_format($total_consumo[4]/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_consumo[4]*$tarifa[0]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_fp[4]*($tarifa[1]*$imposto))/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_i[4]*$tarifa[2]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format(($total_p[4]*$tarifa[3]*$imposto)/1000, 2, ',', ' '); ?></td>
                    <td align="center" class="borda"><?php echo number_format((($total_fp[4]*$tarifa[1]*$imposto)+($total_i[4]*$tarifa[2]*$imposto)+($total_p[4]*$tarifa[3]*$imposto))/1000, 2, ',', ' '); ?></td>
                  </tr>
                </tbody>
              </table>
              <h5><strong>*Cálculo com impostos.</strong><br>
              </h5>
              
            </td>
          </tr>
          <tr>
            <td height="30" align="center" class="borda" <?php echo $barra; ?>><strong>Gráfico de Consumo por Circuito</strong></td>
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
