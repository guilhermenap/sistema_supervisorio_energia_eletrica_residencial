<?php require_once('classes.php');?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
ob_start();
@session_start();
}

if($_POST['usuario'])
{
$user=$_POST['usuario'];
$password=$_POST['senha'];

mysqli_select_db($servidor,$database_servidor);
$query_usuario = "SELECT * FROM usuario WHERE usuario LIKE '$user' AND senha LIKE '$password'";
$usuario = mysqli_query($servidor,$query_usuario);
$row_usuario = mysqli_fetch_assoc($usuario);
$totalRows_usuario = mysqli_num_rows($usuario);	

	if($totalRows_usuario)
	{
		$_SESSION['level'] = 1;	
		$_SESSION['MM_Username']=$row_usuario['usuario'];
		echo '<script>window.location="index.php";</script>';
	}
	else
	{
	echo '<script>alert("Usuário ou senha invalido.");</script>';	
	}
	
}
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
-->
</style>
</head>

<body >
<?php include("cima.php"); ?>
<table width="714" border="0" align="center">
  <tr>
    <td width="785"><form action="index2.php" method="post" name="form1" id="form1">
      <input name="env" type="hidden" id="env" value="1" />
      <table width="192" border="0" align="center">
        <tr>
          <td width="41">Usuário:</td>
          <td width="141"><label>
            <input type="text" name="usuario" id="usuario" />
          </label></td>
        </tr>
        <tr>
          <td>Senha:</td>
          <td><input type="password" name="senha" id="senha" /></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><label>
            <input type="submit" name="submit" id="submit" value="Enviar">
          </label></td>
        </tr>
    </table>
    </form>
      </td>
 </tr>
</table>
</body>
</html>