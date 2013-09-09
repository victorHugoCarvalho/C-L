<?php

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc") ;

// add_lexico.php: Este script cadastra um novo termo no lexico do projeto. 
//                 � passada, atraves da URL, uma variavel $id_projeto, que
//                 indica em que projeto deve ser inserido o novo termo.

session_start();

if (!isset($sucesso))
{
    $sucesso = 'n' ;
}

// Checa se o usu�rio foi autenticado
chkUser("index.php");   

// Conecta ao SGBD
$r = bd_connect() or die("Erro ao conectar ao SGBD");

//Script chamado atrav�s do submit do formul�rio
if (isset($submit))
{
	$ret = checarLexicoExistente($_SESSION['id_projeto_corrente'],$nome);
	if( !isset($listSinonimo))
	{
		$listSinonimo = array();
	}
   
	$retSin = checarSinonimo($_SESSION['id_projeto_corrente'], $listSinonimo);

	if ( ($ret == true) AND ($retSin == true ) )
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
<p style="color: red; font-weight: bold; text-align: center">Este s�mbolo ou sin�nimo j� existe!</p>
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

// Script chamado atrav�s do menu superior

}
else
{        
	$q = "SELECT nome FROM projeto WHERE id_projeto = $id_projeto";
	$qrr = mysql_query($q) or die("Erro ao executar a query");
	$result = mysql_fetch_array($qrr);
	$nome_projeto = $result['nome'];
?>
<html>
<head>
<title>Adicionar L�xico</title>
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
		alert (" Por favor, forne�a o NOME do l�xico.\n O campo NOME � de preenchimento obrigat�rio.");
      	form.nome.focus();
      	return false;
    }
    else
    {
		padrao = /[\\\/\?"<>:|]/;
		nOK = padrao.exec(nome);
		if (nOK)
		{
			window.alert ("O nome do l�xico n�o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
			form.nome.focus();
			return false;
		} 
	}
    
   	if( nocao == "" )
    {
		alert (" Por favor, forne�a a NO��O do l�xico.\n O campo NO��O � de preenchimento obrigat�rio.");
		form.nocao.focus();
		return false;
    }

}

function addSinonimo()
{
	listSinonimo = document.forms[0].elements['listSinonimo[]']; 

	if(document.forms[0].sinonimo.value == "")
	{
		return;
	}
	
	sinonimo = document.forms[0].sinonimo.value;
	padrao = /[\\\/\?"<>:|]/;
	nOK = padrao.exec(sinonimo);
	
	if (nOK)
	{
		window.alert ("O sin�nimo do l�xico n�o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
		document.forms[0].sinonimo.focus();
		return;
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

//Cen�rios -  Incluir L�xico 

//Objetivo:    Permitir ao usu�rio a inclus�o de uma nova palavra do l�xico
//Contexto:    Usu�rio deseja incluir uma nova palavra no l�xico.
//                     Pr�-Condi��o: Login, palavra do l�xico ainda n�o cadastrada
//Atores:         Usu�rio, Sistema
//Recursos:    Dados a serem cadastrados
//Epis�dios:    O sistema fornecer� para o usu�rio uma tela com os seguintes campos de texto:
//               - Entrada L�xico.
//               - No��o.   Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//               - Impacto. Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//              Bot�o para confirmar a inclus�o da nova entrada do l�xico
//              Restri��es: Depois de clicar no bot�o de confirma��o, o sistema verifica se todos
//              os campos foram preenchidos. 
//Exce��o:    Se todos os campos n�o foram preenchidos, retorna para o usu�rio uma mensagem
//              avisando que todos os campos devem ser preenchidos e um bot�o de voltar para a pagina anterior.

?>

</SCRIPT>
<h4>Adicionar S�mbolo</h4>
<br>
<?php
	if ( $sucesso == "s" )
	{
?>
<p style="color: blue; font-weight: bold; text-align: center">S�mbolo inserido com sucesso!</p>
<?php    
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
      <td>Sin�nimos:</td>
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
      <td>No��o:</td>
      <td><textarea cols="51" name="nocao" rows="3" WRAP="SOFT"></textarea></td>
    </tr>
    <tr>
      <td>Impacto:</td>
      <td><textarea  cols="51" name="impacto" rows="3" WRAP="SOFT"></textarea></td>
    </tr>
    <tr>
      <td>Classifica�ao:</td>
      <td><SELECT id='classificacao' name='classificacao' size=1 width="300">
          <OPTION value='sujeito' selected>Sujeito</OPTION>
          <OPTION value='objeto'>Objeto</OPTION>
          <OPTION value='verbo'>Verbo</OPTION>
          <OPTION value='estado'>Estado</OPTION>
        </SELECT></td>
    </tr>
    <tr>
      <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return TestarBranco(this.form);" value="Adicionar S�mbolo">
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
<i><a href="showSource.php?file=add_lexico.php">Veja o c�digo fonte!</a></i>
</body>
</html>
<?php
}
?>
