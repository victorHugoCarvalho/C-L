<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

// alt_conceito.php: Este script faz um pedido de alteracao de um conceito do projeto.
// O usuario recebe um form com o conceito corrente (ou seja com seus campos preenchidos)
// e podera fazer	alteracoes em todos os campos menos no nome.Ao final a tela principal
// retorna para a tela de inicio e a arvore e fechada.O form de alteracao tb e fechado.
// Arquivo chamador: main.php
session_start();
include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

// checks whether the user has been authenticated
chkUser("index.php");

// Connect to the SGBD
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

// Script chamado atraves do submit do formulario
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
else // Script chamado atraves do link no cenario corrente
{

    $nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);

    $query = "SELECT * FROM conceito WHERE id_conceito = $id_conceito";
    $executeQuery = mysql_query($query) or die("Erro ao executar a query");
    $result = mysql_fetch_array($executeQuery);

	// Cen�rio -    Alterar Conceito 
	
	//Objetivo:	Permitir a altera��o de um conceito por um usu�rio
	//Contexto:	Usu�rio deseja alterar conceito previamente cadastrado
	//              Pr�-Condi��o: Login, Cen�rio cadastrado no sistema
	//Atores:	Usu�rio
	//Recursos:	Sistema, dados cadastrados
	//Epis�dios:	O sistema fornecer� para o usu�rio a mesma tela de INCLUIR CEN�RIO,
	//              por�m com os seguintes dados do cen�rio a ser alterado preenchidos
	//              e edit�veis nos seus respectivos campos: Objetivo, Contexto, Atores, Recursos e Epis�dios.
	//              Os campos Projeto e T�tulo estar�o preenchidos, mas n�o edit�veis.
	//              Ser� exibido um campo Justificativa para o usu�rio colocar uma
	//              justificativa para a altera��o feita.
	
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
