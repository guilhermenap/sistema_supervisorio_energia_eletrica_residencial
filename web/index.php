<?php require_once('classes.php');
autorizado('1','level');
?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index2.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
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
?>
   <link rel="stylesheet" href="script/styles.css">
   <script src="script/jquery-latest.min.js" type="text/javascript"></script>
   <script src="script/script.js"></script>
   
   <script> 
   function carregar() {   
	 setInterval(function(){   
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("status").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "frame.php", true);
  xhttp.send();
	 }, 10000);
}

function atualiza()
{
	  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("status").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "frame.php", true);
  xhttp.send();
  
    carregar();
}
   </script>

<body onLoad="javascript:atualiza();">
      <?php include("cima.php"); ?>
<table width="100%" border="0">
  <tbody>
    <tr>
      <td align="center"><a href="index.php?doLogout=true">[Sair]</a></td>
    </tr>
    <tr>
      <td><div id='cssmenu'>
<ul>
   <li><a href='#'><span>Histórico</span></a>
      <ul>
      <li ><a href='alarmes.php'><span>Alarmes</span></a></li>
         <li ><a href='corrente.php'><span>Corrente</span></a></li>
         <li ><a href='tensao.php'><span>Tensão</span></a></li>
          <li ><a href='consumo.php'><span>Consumo</span></a></li>
           <li ><a href='config.php'><span>Configurações</span></a></li>
      </ul>
   </li>
</ul>
</div></td>
    </tr>
  </tbody>
</table>
<div id="status"></div>
<p>&nbsp;</p>
</body>
</html>
