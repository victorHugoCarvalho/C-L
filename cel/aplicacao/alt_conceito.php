<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

// alt_conceito.php:  This script makes a request for alteration of a concept of the project.
//                       The User receives a form with the current concept (ie with completed fields)
//                       and may make changes in all fields,  except the name. At the end of the main screen  
//                       returns to the start screen and the tree is closed. The form of alteration is also closed.
// File that calls: main.php

session_start();
include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

// checks whether the user has been authenticated
chkUser("index.php");

// Connect to the SGBD
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

// Called through the button submit
if (isset($submit)){       

    inserirPedidoAlterarConceito($_SESSION['id_projeto_corrente'],
                                $id_conceito,
                                $nome,
                                $descricao,
                                $namespace,
                                $justificativa,
                                $_SESSION['id_usuario_corrente']);
	?>
<script language="javascript1.3">
	
	opener.parent.frames['code'].location.reload();
	opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');
	
	</script>
<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>
<script language="javascript1.3">
	
	self.close();
	
	</script>
<?php
}
else 	//Script called via the link in the current concept
{

    $nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);

    $query = "SELECT * FROM conceito WHERE id_conceito = $id_conceito";
    $executeQuery = mysql_query($query) or die("Erro ao executar a query");
    $result = mysql_fetch_array($executeQuery);

// 	   Scenery - Changing Concept
//     Purpose: Allow changing a concept for a user
//     Context: User want to change concept previously registered
//     Precondition: Login, Scenario registered in the system
//     Actors: User
//     Features: System, data registered
	
	?>
<html>
<head>
<title>Alterar Conceito</title>
</head>
<body>
<h4>Alterar Conceito</h4>
<br>
<form action="?id_projeto=<?=$id_projeto?>" method="post">
  <table>
    <tr>
      <td>Projeto:</td>
      <td><input disabled size="48" type="text" value="<?=$nome_projeto?>"></td>
    </tr>
    <input type="hidden" name="id_conceitos" value="<?=$result['id_conceito']?>">
    
      <td>Nome:</td>
      <? $result['nome'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['nome']); ?>
      <input type="hidden" name="nome" value="<?=$result['nome']?>">
      <td><input disabled maxlength="128" name="nome2" size="48" type="text" value="<?=$result['nome']?>"></td>
    <tr>
      <td>Descri&ccedil;&atilde;o:</td>
      <? $result['descricao'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['descricao']); ?>
      <td><textarea name="descricao" cols="48" rows="3"><?=$result['descricao']?>
</textarea></td>
    </tr>
    <tr>
      <td>Namespace:</td>
      <? $result['namespace'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['namespace']); ?>
      <td><textarea name="namespace" cols="48" rows="3"><?=$result['namespace']?>
</textarea></td>
    </tr>
    <tr>
      <td>Justificativa para a altera&ccedil;&atilde;o:</td>
      <td><textarea name="justificativa" cols="48" rows="2"></textarea></td>
    </tr>
    <tr>
      <td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Alterar Cen&aacute;rio" onClick="updateOpener()"></td>
    </tr>
  </table>
</form>
<br>
<i><a href="showSource.php?file=alt_cenario.php">Veja o c&oacute;digo fonte!</a></i>
</body>
</html>
<?php
}
	?>
