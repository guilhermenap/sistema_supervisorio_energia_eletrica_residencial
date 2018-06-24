<?php
require_once('classes.php');
autorizado('1','level');
$consumo=addslashes($_GET['consumo']);
if($consumo)
{
mysqli_select_db($servidor, $database_servidor);
mysqli_query($servidor,"INSERT INTO `consumo` (`id`, `hora`, `consumo`, `desativado`) VALUES (NULL, CURRENT_TIMESTAMP, '$consumo', '');");
}

mysqli_select_db($servidor,$database_servidor);
$query_consumo = "SELECT * FROM consumo ORDER BY id DESC LIMIT 1;";
$consumo = mysqli_query($servidor,$query_consumo);
$row_consumo = mysqli_fetch_assoc($consumo);
$totalRows_consumo = mysqli_num_rows($consumo);

$explode=explode('/',$row_consumo['consumo']);
$explode_corrente=explode(',',$explode[0]);
$explode_tensao=explode(',',$explode[1]);
$explode_disjuntor=explode(',',$explode[2]);
?>
<table width="35%" border="0" align="center" class="borda">
  <tbody>
    <tr>
      <td height="32" colspan="2" align="center"><strong>Monitoramento de Circuitos do Quadro Elétrico</strong></td>
    </tr>
    <tr>
      <td colspan="2" align="left">Ultima aquisição: <?php echo date('d/m/Y H:i:s', strtotime($row_consumo['hora'].'- 3 hours'));?></td>
    </tr>
    <tr>
      <td colspan="2" align="center" <?php echo $barra;?> class="borda"><strong>Corrente</strong></td>
    </tr>
    <tr>
      <td width="17%">Circuito 1:</td>
      <td width="83%"><?php echo $explode_corrente[0]; ?> [A]</td>
    </tr>
    <tr>
      <td>Circuito 2:</td>
      <td><?php echo $explode_corrente[1]; ?> [A]</td>
    </tr>
    <tr>
      <td>Circuito 3:</td>
      <td><?php echo $explode_corrente[2]; ?> [A]</td>
    </tr>
    <tr>
      <td>Circuito 4:</td>
      <td><?php echo $explode_corrente[3]; ?> [A]</td>
    </tr>
    <tr>
      <td>Circuito 5:</td>
      <td><?php echo $explode_corrente[4]; ?> [A]</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="center" <?php echo $barra;?> class="borda"><strong>Tensão</strong></td>
    </tr>
    <tr>
      <td>Va</td>
      <td><?php echo $explode_tensao[0]; ?> [V]</td>
    </tr>
    <tr>
      <td>Vb</td>
      <td><?php echo $explode_tensao[1]; ?> [V]</td>
    </tr>
    <tr>
      <td>Vc</td>
      <td><?php echo $explode_tensao[2]; ?> [V]</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="center" <?php echo $barra;?> class="borda"><strong>Estado de Disjuntor</strong></td>
    </tr>
    <tr>
      <td>Disjuntor 1:</td>
      <td><?php if($explode_disjuntor[0]){echo 'Fechado';}else{echo 'Fechado';} ?></td>
    </tr>
    <tr>
      <td>Disjuntor 2:</td>
      <td><?php if($explode_disjuntor[1]){echo 'Fechado';}else{echo 'Fechado';} ?></td>
    </tr>
    <tr>
      <td>Disjuntor 3:</td>
      <td><?php if($explode_disjuntor[2]){echo 'Fechado';}else{echo 'Fechado';} ?></td>
    </tr>
    <tr>
      <td>Disjuntor 4:</td>
      <td><?php if($explode_disjuntor[3]){echo 'Fechado';}else{echo 'Fechado';} ?></td>
    </tr>
    <tr>
      <td>Disjuntor 5:</td>
      <td><?php if($explode_disjuntor[4]){echo 'Fechado';}else{echo 'Fechado';} ?></td>
    </tr>
  </tbody>
</table>