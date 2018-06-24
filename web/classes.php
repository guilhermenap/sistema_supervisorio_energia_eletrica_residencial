<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_servidor = "IP_bd_MYSQL";
$database_servidor = "tcc";
$username_servidor = "usuario_bd_mysql";
$password_servidor = "senha_bd_mysql";
$servidor = mysqli_connect($hostname_servidor, $username_servidor, $password_servidor) or trigger_error(mysqli_error(),E_USER_ERROR); 
?>
<?php
function autorizado($nivel,$tipo_session)
{
	if (!isset($_SESSION)) {
	  session_start();
	}
	$MM_authorizedUsers = "$nivel";
	$MM_donotCheckaccess = "false";
	
	// *** Restrict Access To Page: Grant or deny access to this page
	function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
	  // For security, start by assuming the visitor is NOT authorized. 
	  $isValid = False; 
	
	  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
	  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
	  if (!empty($UserName)) { 
		// Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
		// Parse the strings into arrays. 
		$arrUsers = Explode(",", $strUsers); 
		$arrGroups = Explode(",", $strGroups); 
		if (in_array($UserName, $arrUsers)) { 
		  $isValid = true; 
		} 
		// Or, you may restrict access to only certain users based on their username. 
		if (in_array($UserGroup, $arrGroups)) { 
		  $isValid = true; 
		} 
		if (($strUsers == "") && false) { 
		  $isValid = true; 
		} 
	  } 
	  return $isValid; 
	}
	
	if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION["$tipo_session"])))) {   
	  $MM_qsChar = "?";
	  $MM_referrer = $_SERVER['PHP_SELF'];
	  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
	  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
	  $MM_referrer .= "?" . $QUERY_STRING;
	  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
	 echo '<script>window.location="URL da sua pagina index2.php";</script>';
	  exit;
	}		
}

function insert($tabela,$colunacampotipo,$pagina,$erro,$naorepetir,$campoembranco)
{
global $database_servidor;
global $servidor;	
	
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

$editar=addslashes($_POST["editar"]);

//--------------------------------------------

$c=0;

$branco=explode(',',$campoembranco);


echo '<script>';

echo "function confere(){ total=0;";
	$explode_cad=explode(';',$colunacampotipo);	
	for($i=0;$i<count($explode_cad);$i++)
	{
		$explode2_cad=explode(',',$explode_cad[$i]);
		$campo_cad=$explode2_cad[1];
		
		if($explode_cad[$i])
		{
				if(in_array($campo_cad, $branco))
				{
					
				}
				else
				{
				echo "if(document.getElementById('$campo_cad').value==false){total=1;};";
				}
		}
			
	}
echo '
if(total==1){alert("Existem campos que não foram preenchidos.");}
if(total==false){ document.forms["form1"].submit();};
}</script>';
	if($_POST['env'])
	{
	$explode_cad=explode(';',$colunacampotipo);	
		for($i=0;$i<count($explode_cad);$i++)
		{
			if($explode_cad[$i])
			{
				$explode2_cad=explode(',',$explode_cad[$i]);
				if($_POST["editar"]==false)
				{
					if($c==false)
					{
						if($explode2_cad[2]=='date')
						{
						$transforma_data=explode('/',$_POST[$explode2_cad[1]]);	
						$linha_valor="'".$transforma_data[2].'-'.$transforma_data[1].'-'.$transforma_data[0]."'";
						$linha_tabela='`'.addslashes($explode2_cad[0]).'`';
						$c++;
						}
						else
						{
						$linha_valor="'".addslashes($_POST[$explode2_cad[1]])."'";
						$linha_tabela='`'.addslashes($explode2_cad[0]).'`';
						$c++;
						}
					}
					else
					{
						if($explode2_cad[2]=='date')
						{
						$transforma_data=explode('/',$_POST[$explode2_cad[1]]);	
						$linha_valor=$linha_valor.",'".$transforma_data[2].'-'.$transforma_data[1].'-'.$transforma_data[0]."'";
						$linha_tabela=$linha_tabela.',`'.addslashes($explode2_cad[0]).'`';
						}
						else
						{
						$linha_valor=$linha_valor.",'".addslashes($_POST[$explode2_cad[1]])."'";
						$linha_tabela=$linha_tabela.',`'.addslashes($explode2_cad[0]).'`';
						}
					}
				
				
				}
				else
				{
					
					if($c==false)
					{
						$linha_valor="'".addslashes(nl2br($_POST[$explode2_cad[1]]))."'";
						$linha_tabela='`'.addslashes($explode2_cad[0]).'` = '.$linha_valor;
						$c++;
					}
					else
					{
						$linha_valor="'".addslashes(nl2br($_POST[$explode2_cad[1]]))."'";
						$linha_tabela=$linha_tabela.',`'.addslashes($explode2_cad[0]).'` = '.$linha_valor;
					}
				}
			
			}
		}
		if($erro==false)
		{
			
				################################ Confere se existe campo repetido	
				$var_existe=0;
				if($naorepetir)
				{
				$separar=explode(';',$naorepetir);	
				
					if(count($separar))
					{
						for($d=0;$d<count($separar);$d++)
						{
							if($separar[$d])
							{
								$separar2=explode(',',$separar[$d]);############ Separa[0] - Coluna ### Separa[1] - Campo form
								$coluna=$separar2[0];
								
								$valor=$_POST[$separar2[1]];
							
								
								if($editar)
								{
								mysqli_select_db($servidor, $database_servidor);
								$query_dados = mysqli_query($servidor,"SELECT * FROM $tabela WHERE $coluna = '$valor' AND desativado = 0 AND id NOT LIKE  '$editar'");
								}
								else
								{
								mysqli_select_db($servidor, $database_servidor);
								$query_dados = mysqli_query($servidor,"SELECT * FROM $tabela WHERE $coluna = '$valor' AND desativado = 0");	
								}
								$totalRows_dados = mysqli_num_rows($query_dados);
							
								if($totalRows_dados)
								{
									$var_existe=1;
									$linha_existe=$separar2[0].' '.$linha_existe;
								}
								
							}
						}
					}
				
				}
							
			if($var_existe==false)
			{
			
			if($_POST["editar"]==false)
			{
			$insertSQL = "INSERT INTO  tcc.`$tabela` ($linha_tabela)VALUES ($linha_valor);";	
			}
			else
			{
			$insertSQL = "UPDATE  tcc.`$tabela` SET  $linha_tabela WHERE  `$tabela`.`id` =$editar;";	
			}
			
			mysqli_select_db($servidor, $database_servidor);
			$texto=addslashes($insertSQL);
			
			
			mysqli_select_db($servidor, $database_servidor);
			mysqli_query($servidor,"$insertSQL");	
			echo '<script>window.location="'.$pagina.'"</script>';
			}
			else
			{
			echo '<script>alert("Dados não podem ser repetidos: '.$linha_existe.'");window.history.back();</script>';	
			}
		}
		else
		{
			$add = "INSERT INTO  tcc.`$tabela` ($linha_tabela)VALUES ($linha_valor);";
			$up = "UPDATE  tcc.`$tabela` SET  $linha_tabela WHERE  `$tabela`.`id` =$editar;";	
			echo  $add.'</br>';
			echo  $up.'</br>';
		}
	}
}
error_reporting(0);

$barra='style="background-image: linear-gradient(rgb(255, 255, 255) 0%, rgb(241, 241, 241) 50%, rgb(225, 225, 225) 51%, rgb(246, 246, 246) 100%); background-position: initial initial; background-repeat: initial initial;"';
?>