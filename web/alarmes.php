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


mysqli_select_db($servidor,$database_servidor);
$query_consumo = "SELECT * FROM consumo WHERE hora > '".date('Y-m').'-01 00:00:00'."' ORDER BY id ASC";
$consumo = mysqli_query($servidor,$query_consumo);
$row_consumo = mysqli_fetch_assoc($consumo);
$totalRows_consumo = mysqli_num_rows($consumo);

for($a=0;$a<5;$a++)
{
	$separa=explode(';',$row_config['c'.($a+1)]);
	$limite_corrente[$a]=$separa[1];
}

	$limite_tensao[0]=$row_config['va'];
	$limite_tensao[1]=$row_config['vb'];
	$limite_tensao[2]=$row_config['vc'];
	
	$tensao_f[0]='Va';
	$tensao_f[1]='Vb';
	$tensao_f[2]='Vc';


do{
	$separa1=explode('/',$row_consumo['consumo']);
	$separa2=explode(',',$separa1[0]);//separa corrente por cirucuito
	$separa3=explode(',',$separa1[1]);//separa tensão por circuito
	$separa4=explode(',',$separa1[2]);//separa tensão por circuito
		for($i=0;$i<5;$i++)
		{
		$horario_corrente[strtotime($row_consumo['hora'])-60*60*3][$i]=$separa2[$i];//array de corrente
		}	
		for($i=0;$i<3;$i++)
		{
		$horario_tensao[strtotime($row_consumo['hora'])-60*60*3][$i]=$separa3[$i];//array de tensao
		}	
		for($i=0;$i<5;$i++)
		{
		$horario_disjuntor[strtotime($row_consumo['hora'])-60*60*3][$i]=$separa4[$i];//array de tensao
		}	
	 } while ($row_consumo = mysqli_fetch_assoc($consumo));
	 
///----

	foreach($horario_corrente as $chave=>$circuito)
	{
			
			foreach($circuito as $chave2=>$valor)
			{
				if($valor>$limite_corrente[$chave2] and $ultimo_corrente[$chave2]+60<$chave)
				{
					$erro_corrente[$chave]=$chave2.';'.$valor;
					$ultimo_corrente[$chave2]=$chave;
					
				}
				
			}
	}
	foreach($horario_tensao as $chave=>$circuito)
	{
			
			foreach($circuito as $chave2=>$valor)
			{
				if(($valor>($limite_tensao[$chave2]*1.045) or $valor<($limite_tensao[$chave2]*0.955)) and $valor>10)
				{
					$erro_tensao[$chave]=$chave2.';'.$valor;
					
				}
				if($valor<10)
				{
					if($limite_tensao[$chave2]>0)
					{
					$queda_tensao[$chave]=$chave2;
					
					}
					
				}

			}
	}
	
	foreach($horario_disjutor as $chave=>$circuito)
	{
			
			foreach($circuito as $chave2=>$valor)
			{
				if($valor<10)
				{
					$queda_disjuntor[$chave]=$chave2;
					
				}

			}
	}
	

$inicio=strtotime(date('Y-m').'-01 00:00:00');
$final=strtotime(date('Y-m-t').' 00:00:00');


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
$query_consumo = "SELECT * FROM consumo WHERE hora > '".date('Y-m').'-01 00:00:00'."'  ORDER BY id ASC";
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

$inicio=strtotime(date('Y-m').'-01 00:00:00');
$final=strtotime(date('Y-m-d').' 00:00:00');

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
	}

	$inicio=$inicio+3600;
}

?>
<?php
$inicio=strtotime(date('Y-m').'-01 00:00:00');
$final=strtotime(date('Y-m-d').'00:00:00');


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
            <td height="30" align="center" class="borda" <?php echo $barra; ?>><strong>Alarmes</strong></td>
          </tr>
          <tr>
            <td height="30" align="left" bgcolor="#D5D5D5" class="borda"><strong>Corrente</strong></td>
          </tr>
          <?php
		  foreach($erro_corrente as $chave=>$valor)
		  {
			  $separa=explode(';',$valor);
		  ?>
          <tr>
            <td height="30" align="left" class="borda">
			<?php 
				$separa=explode(';',$valor);
				echo $msg=date('d/m/Y H:i',$chave).' - Circuito '.$separa[0].' / Valor: '.$separa[1].'[A]' ;
				if($chave+60>date('d/m/Y H:i'))
				{
					mail($row_config['email'], 'Alarme sistema supervisorio', $msg);
				}
			?></td>
          </tr>
                <?php
		
		  }
		  ?>
                    <tr>
                  <td height="30" align="left" bgcolor="#D5D5D5" class="borda">
                    <strong>Tensão</strong></td>
          </tr>
          <?php
		  foreach($erro_tensao as $chave=>$valor)
		  {
			$separa=explode(';',$valor);
		  ?>
          <tr>
            <td height="30" align="left" class="borda">
            <?php 
			$separa=explode(';',$valor);
			echo $msg=date('d/m/Y H:i',$chave).' - Tensão '.$tensao_f[$separa[0]].' / Valor de leitura: '.$separa[1].'[V] / Variação: '.number_format(((($separa[1]*100)/$limite_tensao[$separa[0]])-100), 2, ',', ' ').' %' ;
				if($chave+60>date('d/m/Y H:i'))
				{
					mail($row_config['email'], 'Alarme sistema supervisorio', $msg);
				}
			?>
            </td>
          </tr>
          <?php
		  }
		  ?>
                    <?php
		  foreach($queda_tensao as $chave=>$valor)
		  {
		  ?>
          <tr>
            <td height="30" align="left" class="borda">
            <?php 
			
			echo $msg=date('d/m/Y H:i',$chave).' - '.$tensao_f[$valor].' sem tensão' ;
				if($chave+60>date('d/m/Y H:i'))
				{
					mail($row_config['email'], 'Alarme sistema supervisorio', $msg);
				}
			?>
            </td>
          </tr>
          <?php
		  }
		  ?>
          
                              <?php
		  foreach($queda_disjuntor as $chave=>$valor)
		  {
		  ?>
          <tr>
            <td height="30" align="left" class="borda">
            <?php 
			
			echo $msg=date('d/m/Y H:i',$chave).' - Disjuntor circuito '.$valor.' desligado.' ;
				if($chave+60>date('d/m/Y H:i'))
				{
					mail($row_config['email'], 'Alarme sistema supervisorio', $msg);
				}
			?>
            </td>
          </tr>
          <?php
		  }
		  ?>
                    <tr>
            <td height="30" align="left" bgcolor="#D5D5D5" class="borda"><strong>Consumo</strong></td>
          </tr>
                  
                  <?php
				  $explode=explode(',',$row_config['valor']);
				  for($i=0;$i<5;$i++)
				  {
					  
					  if((($total_consumo[$i]*$tarifa[0]*$imposto)/1000)>$explode[$i] and $explode[$i]>0)
					  {
					  ?>
				    <tr>
                      <td height="30" align="left" class="borda"><?php echo 'O circuito '.($i+1).' ultrapassou o limite de R$'.number_format($explode[$i], 2, ',', ' ').' chegando ao valor de R$'.number_format(($total_consumo[0]*$tarifa[0]*$imposto)/1000, 2, ',', ' ').' na tarifa convencional'; ?></td>
                    </tr>
                    <?php
					  }
					  
					  if(((($total_fp[$i]*$tarifa[1]*$imposto)+($total_i[$i]*$tarifa[2]*$imposto)+($total_p[$i]*$tarifa[3]*$imposto))/1000)>$explode[$i] and $explode[$i]>0)
					  {
					  ?>
				    <tr>
                      <td height="30" align="left" class="borda"><?php echo 'O circuito '.($i+1).' ultrapassou o limite de R$'.number_format($explode[$i], 2, ',', ' ').' chegando ao valor de R$'.number_format((($total_fp[$i]*$tarifa[1]*$imposto)+($total_i[$i]*$tarifa[2]*$imposto)+($total_p[$i]*$tarifa[3]*$imposto))/1000, 2, ',', ' ').' na tarifa branca'; ?></td>
                    </tr>
                    <?php
					  }
				  }
				  ?>
        </tbody>
      </table>
      <p>&nbsp;</p>
    <p>&nbsp;</p>
  </body>
  
</html>
