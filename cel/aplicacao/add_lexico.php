<?php

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc") ;

// add_lexico.php: This script registers a new term in the project lexicon.
//                 The variable named $id_projeto, passed trough the URL,
//                 indicates the project wich the new term should be registered

session_start();

define('NO', 'n');
define('YES', 's');

if (!isset($success))
{
    $success = NO ;
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
	$lexiconExists = checkLexiconExists($_SESSION['id_projeto_corrente'],$name);
	if( !isset($listSynonym))
	{
		$listSynonym = array();
	}
	else
	{
		//Nothing to do.
	}
	
	$synonymExists = checkSynonymExists($_SESSION['id_projeto_corrente'], $listSynonym);

	if ( ($lexiconExists == false) AND ($synonymExists == false ) )
	{
		$id_usuario_corrente = $_SESSION['id_usuario_corrente'];        
		addLexiconInsertRequest($id_projeto,$name,$notion,$impact,$id_usuario_corrente, $listSynonym, $classification) ;
	}
	else
	{
		?>
		<html>
			<head>
				<title>Projeto</title>
			</head>
			<body bgcolor="#FFFFFF">
				<p style="color: red; font-weight: bold; text-align: center">Este s&iacute;mbolo ou sin&ocirc;nimo j&aacute; existe!</p>
				<br>
				<br>
				<center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
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
		location.href = "add_lexico.php?id_projeto=<?=$id_projeto?>&success=s"; 
	</script>
	<?php

	// Script called via the top menu
}
else
{        
	$query = "SELECT nome FROM projeto WHERE id_projeto = $id_projeto";
	$ExecuteQuery = mysql_query($query) or die("Erro ao executar a query");
	$result = mysql_fetch_array($ExecuteQuery);
	$nameProject = $result['nome'];
?>
<html>
	<head>
		<title>Adicionar L&eacute;xico</title>
	</head>
	<body>
		<script language="JavaScript">
			<!--
			function blankTest(form)
			{
				name  = form.name.value;
				notion = form.notion.value;

				if (name == "")
				{ 
				alert (" Por favor, forne\u00e7a o NOME do l\u00e9xico.\n O campo NOME deve ser preenchido obrigatoriamente.");
			      	form.name.focus();
			      	return false;
			    }
			    else
			    {
					pattern = /[\\\/\?"<>:|]/;
					notOnPattern = pattern.exec(name);
					if (notOnPattern)
					{
						window.alert ("O nome do l\u00e9xico n\u00e3o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
						form.name.focus();
						return false;
					}
					else
					{
					    //Nothing to do.
					} 
				}
   
			   	if( notion == "" )
			    {
					alert (" Por favor, forne\u00e7a a NO\u00c7\u00c3O do l\u00e9xico.\n O campo NO\u00c7\u00c3O deve ser preenchido obrigatoriamente.");
					form.notion.focus();
					return false;
			    }
			   	else
				{
				    //Nothing to do.
				}
			}
			
			function addSynonym()
			{
				listSynonym = document.forms[0].elements['listSynonym[]']; 
			
				if(document.forms[0].synonyms.value == "")
				{
					return;
				}
				else
				{
				    //Nothing to do.
				}
				
				synonyms = document.forms[0].synonyms.value;
				pattern = /[\\\/\?"<>:|]/;
				notOnPattern = pattern.exec(synonyms);
				
				if (notOnPattern)
				{
					window.alert ("O sin\u00f4nimo do l\u00e9xico n\u00e3o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
					document.forms[0].synonyms.focus();
					return;
				}
				else
				{
				    //Nothing to do.
				} 
					
				listSynonym.options[listSynonym.length] = new Option(document.forms[0].synonyms.value, document.forms[0].synonyms.value);
				document.forms[0].synonyms.value = "";
				document.forms[0].synonyms.focus();
			}
			
			function removeSynonym()
			{
				listSynonym = document.forms[0].elements['listSynonym[]']; 
				
				if (listSynonym.selectedIndex == -1)
				{
					return;
				}
				else
				{
					listSynonym.options[listSynonym.selectedIndex] = null;
				}
				
				delSinonimo();
			}
			
			function doSubmit()
			{
				listSynonym = document.forms[0].elements['listSynonym[]']; 
				
				for(var i = 0; i < listSynonym.length; i++)
				{ 
					listSynonym.options[i].selected = true;
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
		if ($success == YES)
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
		      		<td><input disabled size="48" type="text" value="<?=$nameProject?>"></td>
		    	</tr>
		    	<tr>
		      		<td>Nome:</td>
		      		<td><input size="48" name="name" type="text" value=""></td>
		    	</tr>
			    <tr valign="top">
					<td>Sin&ocirc;nimos:</td>
				    <td width="0%"><input name="synonyms" size="15" type="text" maxlength="50">
				    	&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Adicionar" onClick="addSynonym()">
				      	&nbsp;&nbsp;<input type="button" value="Remover" onClick="removeSynonym()">
				      	&nbsp;
				    </td>
			    </tr>
			    <tr>
			    	<td></td>
			    	<td width="100%"><left>
			        	<select multiple name="listSynonym[]"  style="width: 400px;"  size="5"></select>
			        	</left>
			        	<br>
			        </td>
			    	<tr> </tr>
			     </tr>
			     <tr>
			     	<td>No&ccedil;&atilde;o:</td>
			     	<td><textarea cols="51" name="notion" rows="3" WRAP="SOFT"></textarea></td>
			   	 </tr>
			     <tr>
			     	<td>Impacto:</td>
			     	<td><textarea  cols="51" name="impact" rows="3" WRAP="SOFT"></textarea></td>
			     </tr>
			     <tr>
			     	<td>Classifica&ccedil;&atilde;o:</td>
			     	<td><SELECT id='classification' name='classification' size=1 width="300">
			        	<OPTION value='subject' selected>Sujeito</OPTION>
			        	<OPTION value='objective'>Objeto</OPTION>
			        	<OPTION value='verb'>Verbo</OPTION>
			        	<OPTION value='state'>Estado</OPTION>
			        	</SELECT>
			        </td>
			    </tr>
			    <tr>
			    	<td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return blankTest(this.form);" value="Adicionar S&iacute;mbolo">
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
