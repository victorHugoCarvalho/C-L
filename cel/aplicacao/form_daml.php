<?php 

session_start(); 

include_once("bd.inc"); 

$link = bd_connect();
?> 

<html> 

<body> 

    <head> 
        <title>Gerar DAML</title> 
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
    </head>

<form action="gerador_daml.php" method="post"> 

    <h2>Propriedades da ontologia:</h2> 
<?php 

//Objetivo: Gerar Relatórios DAML

   $today = getdate();  

// Recupera nome do usuário
   $sql_user = "select nome from usuario where id_usuario='". $_SESSION['id_usuario_corrente'] . "';";
   $query_user = mysql_query($sql_user) or die("Erro ao verificar usuário!". mysql_error());  
   $result = mysql_fetch_array($query_user); 
   $usuario = $result[0];

mysql_close($link);

?> 

    &nbsp;Data da Versão: <?= $today['mday'];?>/<?= $today['mon'];?>/<?= $today['year'];?> 
    <input type="hidden" name="data_dia" size="3" value="<?= $today['mday'];?>"> 
    <input  type="hidden" name="data_mes" size="3" value="<?= $today['mon'];?>"> 
    <input type="hidden" name="data_ano" size="6" value="<?= $today['year'];?>"> 

<p>
<table>
<tr><td>Título:          </td><td><input type="text" name="title" size="15">              </td></tr>
<tr><td>Assunto:      </td><td><input type="text" name="subject" size="50">       </td></tr>
<tr><td>Descrição:  </td><td><input type="text" name="description" size="50">  </td></tr>   
<tr><td>Usuário:      </td><td><input type="text" name="user" value= "<?=$usuario?>" size="50">  </td></tr>   
<tr><td>Versão:       </td><td><input type="text" name="versionInfo" size="15">   </td></tr>
</table>
<p>
   <input type="submit" value="Gerar DAML"> 

</p> 

</form> 
    <br><i><a href="showSource.php?file=form_daml.php">Veja o código fonte!</a></i> 
</body> 

</html> 