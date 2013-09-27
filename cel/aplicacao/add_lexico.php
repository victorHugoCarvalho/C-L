<?php

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc") ;

// add_lexico.php: Este script cadastra um novo termo no lexico do projeto. 
//                 � passada, atraves da URL, uma variavel $id_projeto, que
//                 indica em que projeto deve ser inserido o novo termo.

session_start();

define('NO', 'n');
define('YES', 's');

if (!isset($sucesso))
{
    $sucesso = NO ;
}
else
{
	//Nothing to do.
}


chkUser("index.php");   // checks whether the user has been authenticated

// Connect to the SGBD
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

//Called through the button submit
if (isset($submit))
{
	$existenceLexico = checarLexicoExistente($_SESSION['id_projeto_corrente'],$nome);
	if( !isset($listSinonimo))
	{
		$listSinonimo = array();
	}
	else
	{
		//Nothing to do.
	}
	
	$existenceSynonyms = checarSinonimo($_SESSION['id_projeto_corrente'], $listSinonimo);

	if ( ($existenceLexico == true) AND ($existenceSynonyms == true ) )
	{
		$id_usuario_corrente = $_SESSION['id_usuario_corrente'];        
		inserirPedidoAdicionarLexico($id_projeto,$nome,$nocao,$impacto,$id_usuario_corrente, $listSinonimo, $classificacao) ;
	}
	else
	{
?>
<html>
<head>
<title>Projeto</title>
</head>
<body bgcolor="#FFFFFF">
<p style="color: red; font-weight: bold; text-align: center">Este simbolo ou sinonimo j&aacute; existe!</p>
<br>
<br>
<center>
  <a href="JavaScript:window.history.go(-1)">Voltar</a>
</center>
</body>
</html>
<?php
		return;
	}
	
	$ipValor = CELConfig_ReadVar("HTTPD_ip") ;
?>
<script language="javascript1.2">

	opener.parent.frames['code'].location.reload();
	opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');
	location.href = "add_lexico.php?id_projeto=<?=$id_projeto?>&sucesso=s"; 

</script>
<?php

// Script called via the top menu
}
else
{        
	$query = "SELECT nome FROM projeto WHERE id_projeto = $id_projeto";
	$ExecuteQuery = mysql_query($query) or die("Erro ao executar a query");
	$result = mysql_fetch_array($ExecuteQuery);
	$nome_projeto = $result['nome'];
?>
<html>
<head>
<title>Adicionar L&eacute;xico</title>
</head>
<body>
<script language="JavaScript">
<!--

function TestarBranco(form)
{
	nome  = form.nome.value;
	nocao = form.nocao.value;

	if (nome == "" )
	{ 
		alert (" Por favor, forne�a o NOME do lexico.\n O campo NOME deve preenchimento obrigatoriamente.");
      	form.nome.focus();
      	return false;
    }
    else
    {
		padrao = /[\\\/\?"<>:|]/;
		nOK = padrao.exec(nome);
		if (nOK)
		{
			window.alert ("O nome do lexico n�o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
			form.nome.focus();
			return false;
		}
		else
		{
		    //Nothing to do.
		} 
	}
    
   	if( nocao == "" )
    {
		alert (" Por favor, forne�a a NOCAO do lExico.\n O campo NOCAO deve preenchimento obrigatoriamente.");
		form.nocao.focus();
		return false;
    }
   	else
	{
	    //Nothing to do.
	}

}

function addSinonimo()
{
	listSinonimo = document.forms[0].elements['listSinonimo[]']; 

	if(document.forms[0].sinonimo.value == "")
	{
		return;
	}
	else
	{
	    //Nothing to do.
	}
	
	sinonimo = document.forms[0].sinonimo.value;
	padrao = /[\\\/\?"<>:|]/;
	nOK = padrao.exec(sinonimo);
	
	if (nOK)
	{
		window.alert ("O sinonimo do lexico nao pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
		document.forms[0].sinonimo.focus();
		return;
	}
	else
	{
	    //Nothing to do.
	} 
		
	listSinonimo.options[listSinonimo.length] = new Option(document.forms[0].sinonimo.value, document.forms[0].sinonimo.value);
	document.forms[0].sinonimo.value = "";
	document.forms[0].sinonimo.focus();
}

function delSinonimo()
{
	listSinonimo = document.forms[0].elements['listSinonimo[]']; 
	
	if (listSinonimo.selectedIndex == -1)
	{
		return;
	}
	else
	{
		listSinonimo.options[listSinonimo.selectedIndex] = null;
	}
	
	delSinonimo();
}

function doSubmit()
{
	listSinonimo = document.forms[0].elements['listSinonimo[]']; 
	
	for(var i = 0; i < listSinonimo.length; i++)
	{ 
		listSinonimo.options[i].selected = true;
	}
	
	return true;
}

//-->

<?php

//Cenarios -  Incluir Lexico 

//Objetivo:    Permitir ao usu�rio a inclus�o de uma nova palavra do lexico
//Contexto:    Usuario deseja incluir uma nova palavra no lexico.
//Pre-Condi�oes: Login, palavra do lexico ainda nao cadastrada
//Atores:         Usuario, Sistema
//Recursos:    Dados a serem cadastrados
//Episodios:    O sistema fornecer para o usuario uma tela com os seguintes campos de texto:
//               - Entrada Lexico.
//               - No�aoo.   Restri�ao: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//               - Impacto. Restri�ao: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//              Bot�o para confirmar a inclus�o da nova entrada do lexico
//              Restri�ao: Depois de clicar no bot�o de confirma�ao, o sistema verifica se todos
//              os campos foram preenchidos. 
//Exce�ao:    Se todos os campos nao foram preenchidos, retorna para o usuario uma mensagem
//              avisando que todos os campos devem ser preenchidos e um botoo de voltar para a pagina anterior.

?>

</SCRIPT>
<h4>Adicionar S&iacute;mbolo</h4>
<br>
<?php
	if ( $sucesso == YES )
	{
?>
<p style="color: blue; font-weight: bold; text-align: center">S&iacute;mbolo inserido com sucesso!</p>
<?php    
	}
	else
	{
		//Nothing to do.
	}
?>
<form action="?id_projeto=<?=$id_projeto?>" method="post" onSubmit="return(doSubmit());">
  <table>
    <tr>
      <td>Projeto:</td>
      <td><input disabled size="48" type="text" value="<?=$nome_projeto?>"></td>
    </tr>
    <tr>
      <td>Nome:</td>
      <td><input size="48" name="nome" type="text" value=""></td>
    </tr>
    <tr valign="top">
      <td>Sin&ocirc;nimos:</td>
      <td width="0%"><input name="sinonimo" size="15" type="text" maxlength="50">
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input type="button" value="Adicionar" onClick="addSinonimo()">
        &nbsp;&nbsp;
        <input type="button" value="Remover" onClick="delSinonimo()">
        &nbsp; </td>
    </tr>
    <tr>
      <td></td>
      <td width="100%"><left>
          <select multiple name="listSinonimo[]"  style="width: 400px;"  size="5">
          </select>
        </left>
        <br></td>
    <tr> </tr>
      </tr>
    
    <tr>
      <td>No&ccedil;&atilde;o:</td>
      <td><textarea cols="51" name="nocao" rows="3" WRAP="SOFT"></textarea></td>
    </tr>
    <tr>
      <td>Impacto:</td>
      <td><textarea  cols="51" name="impacto" rows="3" WRAP="SOFT"></textarea></td>
    </tr>
    <tr>
      <td>Classifica&ccedil;&atilde;o:</td>
      <td><SELECT id='classificacao' name='classificacao' size=1 width="300">
          <OPTION value='sujeito' selected>Sujeito</OPTION>
          <OPTION value='objeto'>Objeto</OPTION>
          <OPTION value='verbo'>Verbo</OPTION>
          <OPTION value='estado'>Estado</OPTION>
        </SELECT></td>
    </tr>
    <tr>
      <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return TestarBranco(this.form);" value="Adicionar S&iacute;mbolo">
        <BR>
        <BR>
        </script> 
        <!--            <A HREF="RegrasLAL.html" TARGET="new">Ver Regras do LAL</A><BR>   --> 
        <A HREF="#" OnClick="javascript:open( 'RegrasLAL.html' , '_blank' , 'dependent,height=380,width=520,titlebar' );"> Veja as regras do <i>LAL</i></A></td>
    </tr>
  </table>
</form>
<center>
  <a href="javascript:self.close();">Fechar</a>
</center>
<br>
<i><a href="showSource.php?file=add_lexico.php">Veja o c&oacute;digo fonte!</a></i>
</body>
</html>
<?php
}
?>
