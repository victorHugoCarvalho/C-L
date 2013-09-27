<?php

session_start();

/* vim: set expandtab tabstop=4 shiftwidth=4: */

// class add_cenario.php: This script registers a new term in the lexicon of the project. 
//Is sent through the URL, a variable $ id_project, which indicates that indicates 
//where the new term should be inserted.

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");


chkUser("index.php");  // checks whether the user has been authenticated   

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

// Connect to the SGBD
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) 
{
    $existenceScenery = checarCenarioExistente($_SESSION['id_projeto_corrente'],$title);

    if ($existenceScenery == true)
    {    
        print("<!-- Tentando Inserir Cenario --><BR>");

        /* Substitui todas as ocorrencias de ">" e "<" por " " */
        $title       = str_replace( ">" , " " , str_replace ( "<" , " " , $title      ) ) ;
        $objective   = str_replace( ">" , " " , str_replace ( "<" , " " , $objective  ) ) ;
        $context     = str_replace( ">" , " " , str_replace ( "<" , " " , $context    ) ) ;
        $actors      = str_replace( ">" , " " , str_replace ( "<" , " " , $actors     ) ) ;
        $resources   = str_replace( ">" , " " , str_replace ( "<" , " " , $resources  ) ) ;
        $exception   = str_replace( ">" , " " , str_replace ( "<" , " " , $exception  ) ) ;
        $episodes     = str_replace( ">" , " " , str_replace ( "<" , " " , $episodes  ) ) ;
        inserirPedidoAdicionarCenario($_SESSION['id_projeto_corrente'],       
                                      $title,
                                      $objective,
                                      $context,
                                      $actors,
                                      $resources,
                                      $exception,
                                      $episodes,
                                      $_SESSION['id_usuario_corrente']);
     	print("<!-- Cenario Inserido Com Sucesso! --><BR>");
     }
     else
     {
     	?>
		<html>
			<head>
				<title>Projeto</title>
			</head>
			<body bgcolor="#FFFFFF">
				<p style="color: red; font-weight: bold; text-align: center">Este cen&aacute;rio j&aacute; existe!</p>
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
	?>
	
	<script language="javascript1.2">
	
	opener.parent.frames['code'].location.reload();
	opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');
	//self.close();
	//location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") ); ?>add_cenario.php?id_projeto=<?=$id_projeto?>&sucesso=s" ;
	
	
	location.href = "add_cenario.php?id_projeto=<?=$id_projeto?>&success=s";
	
	</script>
<?php

} 
else // Script called via the top menu
{
	$nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
	?>
	<html>
		<head>
			<title>Adicionar Cen&aacute;rio</title>
		</head>
	<body>
		<script language="JavaScript">
			<!--
			function TestarBranco(form)
			{
				titulo = form.titulo.value;
				objetivo = form.objetivo.value;
				contexto = form.contexto.value;
				
				if ((titulo == ""))
				{ 
					alert ("Por favor, digite o titulo do cen�rio.")
					form.titulo.focus()
					return false;
				} 
				else
				{
					padrao = /[\\\/\?"<>:|]/;
					OK = padrao.exec(titulo);
					if (OK)
					{
						window.alert ("O t�tulo do cen�rio n�o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
						form.titulo.focus();
						return false;
					} 
					else
					{
					    //Nothing to do.
					}
				}
				      
				if ((objetivo == ""))
				{
					alert ("Por favor, digite o objetivo do cen�rio.")
				    form.objetivo.focus()
				    return false;
				}
				else
				{
				    //Nothing to do.
				}    
				      
				if ((contexto == ""))
				{
					alert ("Por favor, digite o contexto do cen�rio.")
				    form.contexto.focus()
				    return false;
				}
				else
				{
				    //Nothing to do.
				}        
			}
			//-->
			
			<?php
			
			// Cen�rio -  Incluir Cen�rio 
			
			//Objetivo:        Permitir ao usu�rio a inclus��o de um novo cen�rio
			//Contexto:        Usu�rio deseja incluir um novo cen�rio.
			//Pr�-Condi��o: Login, cen�rio ainda n�o cadastrado
			//Atores:        Usu�rio, Sistema
			//Recursos:        Dados a serem cadastrados
			//Epis�dios:    O sistema fornecer� para o usu�rio uma tela com os seguintes campos de texto:
			//                - Nome Cen�rio
			//                - Objetivo.  Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
			//                - Contexto.  Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
			//                - Atores.    Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
			//                - Recursos.  Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
			//                - Exce��o.   Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
			//                - Epis�dios. Restri��o: Caixa de texto com pelo menos 16 linhas de escrita vis�veis
			//                - Bot�o para confirmar a inclus��o do novo cen�rio
			//              Restri��es: Depois de clicar no bot�o de confirma��o,
			//                          o sistema verifica se todos os campos foram preenchidos. 
			// Exce��o:        Se todos os campos n�o foram preenchidos, retorna para o usu�rio uma mensagem avisando
			//              que todos os campos devem ser preenchidos e um bot�o de voltar para a pagina anterior.
			
			?>
		</SCRIPT>
		
		<h4>Adicionar Cen&aacute;rio</h4>
		<br>
		<?php
		if ( $success == YES )
		{
		?>
		<p style="color: blue; font-weight: bold; text-align: center">Cen&aacute;rio inserido com sucesso!</p>
		<?php    
		}
		?>
		<form action="" method="post">
		  <table>
		    <tr>
		      <td>Projeto:</td>
		      <td><input disabled size="51" type="text" value="<?=$nome_projeto?>"></td>
		    </tr>
		    
		      <td>T&iacute;tulo:</td>
		      <td><input size="51" name="title" type="text" value=""></td>
		    <tr>
		      <td>Objetivo:</td>
		      <td><textarea cols="51" name="objective" rows="3" WRAP="SOFT"></textarea></td>
		    </tr>
		    <tr>
		      <td>Contexto:</td>
		      <td><textarea cols="51" name="context" rows="3" WRAP="SOFT"></textarea></td>
		    </tr>
		    <tr>
		      <td>Atores:</td>
		      <td><textarea cols="51" name="actors" rows="3" WRAP="SOFT"></textarea></td>
		    </tr>
		    <tr>
		      <td>Recursos:</td>
		      <td><textarea cols="51" name="resources" rows="3" WRAP="SOFT"></textarea></td>
		    </tr>
		    <tr>
		      <td>Exce&ccedil;&atilde;o:</td>
		      <td><textarea cols="51" name="exception" rows="3" WRAP="SOFT"></textarea></td>
		    </tr>
		    <tr>
		      <td>Epis&oacute;dios:</td>
		      <td><textarea cols="51" name="episodes" rows="5" WRAP="SOFT"></textarea></td>
		    </tr>
		    <tr>
		      <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return TestarBranco(this.form);" value="Adicionar Cen&aacute;rio"></td>
		    </tr>
		  </table>
		</form>
		<center>
		  <a href="javascript:self.close();">Fechar</a>
		</center>
		<br>
		<i><a href="showSource.php?file=add_cenario.php">Veja o c&oacute;digo fonte!</a></i>
	</body>
</html>
<?php
}
	?>
