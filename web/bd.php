<?php require_once('classes.php');
$consumo=addslashes($_GET['consumo']);
$hash=addslashes($_GET['hash']);//
if($consumo and $hash=='3452345246456')
{
mysqli_select_db($servidor, $database_servidor);//conecta com o banco de dados
mysqli_query($servidor,"INSERT INTO `consumo` (`id`, `hora`, `consumo`, `desativado`) VALUES (NULL, CURRENT_TIMESTAMP, '$consumo', '');");//registra os dados
}
?>
