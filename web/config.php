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
if($_POST['env'])
{
$_POST['c1']=$_POST['va_c1'].','.$_POST['vb_c1'].','.$_POST['vc_c1'].';'.$_POST['d1'];

$_POST['c2']=$_POST['va_c2'].','.$_POST['vb_c2'].','.$_POST['vc_c2'].';'.$_POST['d2'];

$_POST['c3']=$_POST['va_c3'].','.$_POST['vb_c3'].','.$_POST['vc_c3'].';'.$_POST['d3'];

$_POST['c4']=$_POST['va_c4'].','.$_POST['vb_c4'].','.$_POST['vc_c4'].';'.$_POST['d4'];

$_POST['c5']=$_POST['va_c5'].','.$_POST['vb_c5'].','.$_POST['vc_c5'].';'.$_POST['d5'];

$_POST['tarifa']=$_POST['tarifa_conv'].','.$_POST['tarifa_b_fp'].','.$_POST['tarifa_b_i'].','.$_POST['tarifa_b_p'].','.$_POST['imposto'];

$_POST['valor']=$_POST['valor1'].','.$_POST['valor2'].','.$_POST['valor3'].','.$_POST['valor4'].','.$_POST['valor5'];
}
insert('configuracao','c1,c1,text;c2,c2,text;c3,c3,text;c4,c4,text;c5,c5,text;va,va,text;vb,vb,text;vc,vc,text;tarifa,tarifa,text;email,email,text;valor,valor,text','index.php',0,'','c1,c2,c3,c4,c5,va,vb,vc,tarifa,valor');


mysqli_select_db($servidor,$database_servidor);
$query_config = "SELECT * FROM configuracao";
$config = mysqli_query($servidor,$query_config);
$row_config = mysqli_fetch_assoc($config);
$totalRows_config = mysqli_num_rows($config);

$tensao1=explode(';',$row_config['c1']);
$tensao2=explode(';',$row_config['c2']);
$tensao3=explode(';',$row_config['c3']);
$tensao4=explode(';',$row_config['c4']);
$tensao5=explode(';',$row_config['c5']);

$circuito1=explode(',',$tensao1[0]);
$circuito2=explode(',',$tensao2[0]);
$circuito3=explode(',',$tensao3[0]);
$circuito4=explode(',',$tensao4[0]);
$circuito5=explode(',',$tensao5[0]);

$tarifa=explode(',',$row_config['tarifa']);

$valor=explode(',',$row_config['valor']);

?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
<?php for($b=0;$b<5;$b++) { ?>	
	<script type="text/javascript">
google.charts.load('current', {packages: ['corechart', 'bar']});
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
          title: 'Hora do dia',
         
          viewWindow: {
            min: [00, 00, 0],
            max: [24, 00, 0]
          }
        },
        vAxis: {
          title: 'Corrente [A]',
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

      <body>
      <?php include("cima.php"); ?>
      <blockquote>
        <form name="form1" method="post" action="">
          <table width="80%" border="0" align="center">
            <tbody>
              <tr>
                <td colspan="2" align="center"><a href="index.php"><br>
                  Voltar<br>
                  <br>
                </a></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="center" class="borda" <?php echo $barra; ?>><strong>Configurações</strong></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" bgcolor="#E1E1E1" class="borda"><strong>Circuito 1</strong></td>
              </tr>
              <tr>
                <td colspan="2" align="left" class="borda"><p><strong>Tensão</strong><br>
                  Va: 
                  <input name="va_c1" type="checkbox" id="va_c1" value="1" <?php if($circuito1[0]){ echo "checked";}?> >
                  <br>
                  Vb: 
                  <input name="vb_c1" type="checkbox" id="vb_c1" value="1" <?php if($circuito1[1]){ echo "checked";}?>>
                  <br>
                  Vc: 
                  <input name="vc_c1" type="checkbox" id="vc_c1" value="1" <?php if($circuito1[2]){ echo "checked";}?>>
                  </p>
                  <p>Disjuntor: 
                    
                    <input name="d1" type="number" id="d1" value="<?php echo $tensao1[1];?>">
                    [A]<br>
                    <br>
                    Gasto Max. Mensal: R$
                    <input name="valor1" type="number" id="valor1" value="<?php echo $valor[0];?>">
                    <br>
                    <br>
                </p></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" bgcolor="#E1E1E1" class="borda"><strong>Circuito 2</strong></td>
              </tr>
              <tr>
                <td colspan="2" align="left" class="borda"><p><strong>Tensão</strong><br>
                  Va:
                  <input name="va_c2" type="checkbox" id="va_c2" value="1" <?php if($circuito2[0]){ echo "checked";}?>>
                  <br>
                  Vb:
                  <input name="vb_c2" type="checkbox" id="vb_c2" value="1" <?php if($circuito2[1]){ echo "checked";}?>>
                  <br>
                  Vc:
                  <input name="vc_c2" type="checkbox" id="vc_c2" value="1" <?php if($circuito2[2]){ echo "checked";}?>>
                  </p>
                  <p>Disjuntor:
                    <input name="d2" type="number" id="d2" value="<?php echo $tensao2[1];?>">
                [A]</p>
                <p>Gasto Max. Mensal: R$
                  <input name="valor2" type="number" id="valor2" value="<?php echo $valor[1];?>">
                </p></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" bgcolor="#E1E1E1" class="borda"><strong>Circuito 3</strong></td>
              </tr>
              <tr>
                <td colspan="2" align="left" class="borda"><p><strong>Tensão</strong><br>
                  Va:
                  <input name="va_c3" type="checkbox" id="va_c3" value="1" <?php if($circuito3[0]){ echo "checked";}?>>
                  <br>
                  Vb:
                  <input name="vb_c3" type="checkbox" id="vb_c3" value="1" <?php if($circuito3[1]){ echo "checked";}?>>
                  <br>
                  Vc:
                  <input name="vc_c3" type="checkbox" id="vc_c3" value="1" <?php if($circuito3[2]){ echo "checked";}?>>
                  </p>
                  <p>Disjuntor:
                    <input name="d3" type="number" id="d3" value="<?php echo $tensao3[1];?>">
                [A]</p>
                <p>Gasto Max. Mensal: R$
                  <input name="valor3" type="number" id="valor3" value="<?php echo $valor[2];?>">
                </p></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" bgcolor="#E1E1E1" class="borda"><strong>Circuito 4</strong></td>
              </tr>
              <tr>
                <td colspan="2" align="left" class="borda"><p><strong>Tensão</strong><br>
                  Va:
                  <input name="va_c4" type="checkbox" id="va_c4" value="1" <?php if($circuito4[0]){ echo "checked";}?>>
                  <br>
                  Vb:
  <input name="vb_c4" type="checkbox" id="vb_c4" value="1 <?php if($circuito4[1]){ echo "checked";}?>">
  <br>
                  Vc:
  <input name="vc_c4" type="checkbox" id="vc_c4" value="1" <?php if($circuito4[2]){ echo "checked";}?>>
                </p>
                  <p>Disjuntor:
                    <input name="d4" type="number" id="d4" value="<?php echo $tensao4[1];?>">
                [A]</p>
                <p>Gasto Max. Mensal: R$
                  <input name="valor4" type="number" id="valor4" value="<?php echo $valor[3];?>">
                </p></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" bgcolor="#E1E1E1" class="borda"><strong>Circuito 5</strong></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" class="borda"><p><strong>Tensão</strong><br>
                  Va:
                  <input name="va_c5" type="checkbox" id="va_c5" value="1" <?php if($circuito5[0]){ echo "checked";}?>>
                  <br>
                  Vb:
  <input name="vb_c5" type="checkbox" id="vb_c5" value="1" <?php if($circuito5[1]){ echo "checked";}?>>
  <br>
                  Vc:
  <input name="vc_c5" type="checkbox" id="vc_c5" value="1" <?php if($circuito5[2]){ echo "checked";}?>>
                </p>
                  <p>Disjuntor:
                    <input name="d5" type="number" id="d5" value="<?php echo $tensao5[1];?>">
                [A]</p>
                <p>Gasto Max. Mensal: R$
                  <input name="valor5" type="number" id="valor5" value="<?php echo $valor[4];?>">
                </p></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" bgcolor="#E1E1E1" class="borda"><strong>Tensões</strong></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" class="borda">Va:
                  
                  <input name="va" type="number" id="va" value="<?php echo $row_config["va"];?>">
                   [V]<br>
                  <br>
Vb:
<input name="vb" type="number" id="vb" value="<?php echo $row_config["vb"];?>">
                  [V]<br>
<br>
Vc:
<input name="vc" type="number" id="vc" value="<?php echo $row_config["vc"];?>">
[V]</td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="left" bgcolor="#E1E1E1"  class="borda"><strong>Preço Tarifas</strong></td>
              </tr>
              <tr>
                <td height="30" align="left">Convencional: </td>
                <td align="left"><input name="tarifa_conv" type="number" id="tarifa_conv" value="<?php echo $tarifa[0];?>">
                [R$]</td>
              </tr>
              <tr>
                <td height="30" align="left">Branca FP: </td>
                <td align="left"><input name="tarifa_b_fp" type="number" id="tarifa_b_fp" value="<?php echo $tarifa[1];?>">
                [R$]</td>
              </tr>
              <tr>
                <td height="30" align="left">Branca Interm.: </td>
                <td align="left"><input name="tarifa_b_i" type="number" id="tarifa_b_i" value="<?php echo $tarifa[2];?>">
                [R$]</td>
              </tr>
              <tr>
                <td width="10%" height="30" align="left"><p>Branca Ponta:
                  
                </p></td>
                <td width="90%" align="left"><input name="tarifa_b_p" type="number" id="tarifa_b_p" value="<?php echo $tarifa[3];?>">
                [R$]</td>
              </tr>
              <tr>
                <td height="30" align="center">PIS+COFINS+ICMS: </td>
                <td height="30" align="left"><input name="imposto" type="number" id="imposto" value="<?php echo $tarifa[4];?>">
                  [%]</td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="center">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left">Email:</td>
                <td height="30" align="left"><input name="email" type="text" id="email" value="<?php echo $row_config['email'];?>" size="50"></td>
              </tr>
              <tr>
                <td height="30" colspan="2" align="center"><input name="editar" type="hidden" id="editar" value="<?php echo $row_config['id'];?>">
                <input name="env" type="hidden" id="env" value="1">                  <input type="button" name="button" id="button" value="Enviar" onClick="javascript:confere();"></td>
              </tr>
            </tbody>
          </table>
        </form>
      </blockquote>
      <p>&nbsp;</p>
    <p>&nbsp;</p>
  </body>
  
</html>
