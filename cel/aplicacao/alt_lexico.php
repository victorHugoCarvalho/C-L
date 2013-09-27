<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

// alt_lexico.php: Este script faz um pedido de alteracao de um lexico do projeto.
//                 O usuario recebe um form com o lexico corrente (ou seja, com seus campos preenchidos)
//                 e podera fazer alteracoes em todos os campos menos no nome. Ao final a tela principal
//                 retorna para a tela de inicio e a arvore e fechada. O form de alteracao tb e fechado.
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
if (isset($submit))       
{
	
	if (!isset($listSinonimo))
	{
		$listSinonimo = array();
	}
	
    //tira os sin�nimos caso haja um nulo.
    $count = count($listSinonimo);                                                        
    for ($i = 0; $i < $count; $i++)   
    {
		if ($listSinonimo[$i] == "")
		{
			$listSinonimo = null;	
		}
    }
    //$count = count($listSinonimo);
    
	foreach ($listSinonimo as $key=>$sinonimo)
	{
	    $listSinonimo[$key] = str_replace( ">" , " " , str_replace ("<" , " " , $sinonimo )) ;
	}
	

    inserirPedidoAlterarLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $justificativa, $_SESSION['id_usuario_corrente'], $listSinonimo, $classificacao);
	?>
<html>
<head>
<title>Alterar L&eacute;xico</title>
</head>
<body>
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
else        // Script chamado atraves do link do lexico corrente
{
    $nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    $query = "SELECT * FROM lexico WHERE id_lexico = $id_lexico";
    $executeQuery = mysql_query($query) or die("Erro ao executar a query");
    $result = mysql_fetch_array($executeQuery);
    
	//sinonimos
	//$DB = new PGDB () ;
	//$selectSin = new QUERY ($DB) ;
	//$selectSin->execute("SELECT nome FROM sinonimo WHERE id_lexico = $id_lexico");
	$queySin = "SELECT nome FROM sinonimo WHERE id_lexico = $id_lexico";
	$$executeQuerySin = mysql_query($queySin) or die("Erro ao executar a query");
	//$resultSin = mysql_fetch_array($qrrSin);
	?>
<html>
<head>
<title>Alterar L&eacute;xico</title>
</head>
<body>
<script language="JavaScript">
	<!--
	
	function TestarBranco(form)
	{
		nocao = form.nocao.value;
		
		if(nocao == "")
		{
			alert (" Por favor, forne�a a NO��O do l�xico.\n O campo NO��O � de preenchimento obrigat�rio.");
	      	form.nocao.focus();
	      	return false;
	    }
	}
	
	function addSinonimo()
	{
		listSinonimo = document.forms[0].elements['listSinonimo[]']; 
		
		if (document.forms[0].sinonimo.value == "")
		{
			return;
		}
		
		listSinonimo.options[listSinonimo.length] = new Option(document.forms[0].sinonimo.value, document.forms[0].sinonimo.value);
		document.forms[0].sinonimo.value = "";
		document.forms[0].sinonimo.focus();
	}
	
	function delSinonimo()
	{
		listSinonimo = document.forms[0].elements['listSinonimo[]']; 
		
		if(listSinonimo.selectedIndex == -1)
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
		
		for (var i = 0; i < listSinonimo.length; i++) 
		{
			listSinonimo.options[i].selected = true;
		}
		
		return true;
	}
	
	//-->
	
	<?php
		//Cen�rios -  Alterar L�xico 
		
		//Objetivo:	Permitir a altera��o de uma entrada do dicion�rio l�xico por um usu�rio	
		//Contexto:	Usu�rio deseja alterar um l�xico previamente cadastrado
		//Pr�-Condi��o: Login, l�xico cadastrado no sistema
		//Atores:	Usu�rio
		//Recursos:	Sistema, dados cadastrados
		//Epis�dios:	O sistema fornecer� para o usu�rio a mesma tela de INCLUIR L�XICO,
		//              por�m com os seguintes dados do l�xico a ser alterado preenchidos
		//              e edit�veis nos seus respectivos campos: No��o e Impacto.
		//              Os campos Projeto e Nome estar�o preenchidos, mas n�o edit�veis.
		//              Ser� exibido um campo Justificativa para o usu�rio colocar uma
		//              justificativa para a altera��o feita.	
	
	?>
	
	</SCRIPT>
<h4>Alterar S&iacute;mbolo</h4>
<br>
<form action="?id_projeto=<?=$id_projeto?>" method="post" onSubmit="return(doSubmit());">
  <table>
    <input type="hidden" name="id_lexico" value="<?=$result['id_lexico']?>">
    <tr>
      <td>Projeto:</td>
      <td><input disabled size="48" type="text" value="<?=$nome_projeto?>"></td>
    </tr>
    <tr>
      <td>Nome:</td>
      <td><input disabled maxlength="64" name="nome_visivel" size="48" type="text" value="<?=$result['nome'];?>">
        <input type="hidden"  maxlength="64" name="nome" size="48" type="text" value="<?=$result['nome'];?>"></td>
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
      <td width="100%">
      	<left>
    		<select multiple name="listSinonimo[]"  style="width: 400px;"  size="5">
      		<?php
				while($rowSin = mysql_fetch_array($qrrSin))
	  			{
	  		?>
      			<option value="<?=$rowSin["nome"]?>">
      			<?=$rowSin["nome"]?>
     			 </option>
      			<?php
	 			}
				?>
        	<select>
        </left>
        <br>
      </td>
      
      </tr>
      
        <tr>
        	<td>No&ccedil;&atilde;o:</td>
        	<td><textarea name="nocao" cols="48" rows="3" ><?=$result['nocao'];?></textarea></td>
        </tr>
      
        <tr>
        	<td>Impacto:</td>
        	<td><textarea name="impacto" cols="48" rows="3"><?=$result['impacto'];?></textarea></td>
        </tr>
      
        <tr>
        	<td>Classifica&ccedil;&atilde;o:</td>
        	<td>
        		<SELECT id='classificacao' name='classificacao' size=1 width="300">
	      			<OPTION value='sujeito' <?php if($result['tipo'] == 'sujeito') echo "selected"?>>Sujeito</OPTION>
      				<OPTION value='objeto' <?php if($result['tipo'] == 'objeto') echo "selected"?>>Objeto</OPTION>
      				<OPTION value='verbo' <?php if($result['tipo'] == 'verbo') echo "selected"?>>Verbo</OPTION>
      				<OPTION value='estado' <?php if($result['tipo'] == 'estado') echo "selected"?>>Estado</OPTION>
    			</SELECT>
      		</td>
      	</tr>

    <tr>
      <td>Justificativa para a altera&ccedil;&atilde;o:</td>
      <td><textarea name="justificativa" cols="48" rows="6"></textarea></td>
    </tr>
    <tr>
      <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return TestarBranco(this.form);" value="Alterar S&iacute;mbolo"></td>
    </tr>
  </table>
</form>
<center>
  <a href="javascript:self.close();">Fechar</a>
</center>
<br>
<i><a href="showSource.php?file=alt_lexico.php">Veja o c&oacute;digo fonte!</a></i>
</body>
</html>
<?php
}
?>
