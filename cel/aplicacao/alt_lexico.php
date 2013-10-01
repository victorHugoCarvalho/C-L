<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

// alt_lexico.php: This script makes a request for alteration of a lexicon of the project.
//                     The User receives a form with the current lexicon (ie with completed fields)
//                     and may make changes in all fields except the name. At the end of the main screen
//                     returns to the start screen and the tree is closed. 
//					   The form of alteration is also closed.
//   File that calls: main.php 

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

// checks whether the user has been authenticated
chkUser("index.php");        

// Connect to the SGBD
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

// Called through the button submit
if (isset($submit))       
{
	
	if (!isset($listSinonimo))
	{
		$listSinonimo = array();
	}
	else
	{
		//Nothing to do.
	}
	
    //tira os sinï¿½nimos caso haja um nulo.
    $count = count($listSinonimo);                                                        
    for ($i = 0; $i < $count; $i++)   
    {
		if ($listSinonimo[$i] == "")
		{
			$listSinonimo = null;	
		}
		else
		{
			//Nothing to do.
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
			alert (" Por favor, forne&ccedil;a a no&ccedil;&atilde;o do l&eacute;xico.\n O campo no&ccedil;&atilde;o &eacute; de preenchimento obrigat&oacute;rio.");
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
		
		if (document.forms[0].sinonimo.value == "")
		{
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
//			Scenerys - Change Lexicon 
//    		Purpose: Allow changing a lexicon by the user
//    		Context: User want to change a lexicon previously registered
//    		Precondition: Login lexicon, registered in the system
//    			Actors: User
//     		Features: System, data registered
//     		Episódios:	The system will provide to the user the same screen add_lexico,
//                		but with the following data from the lexical to be changed filled
//                 		and editable in their respective fields: Concept and Impact.
//                 		Project and Name fields will be filled, but not editable.
//                 		Displays a field Rationale for the user to place a
//                 		justification for the change made.	
	
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
