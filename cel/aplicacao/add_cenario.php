<?php

session_start();

/* vim: set expandtab tabstop=4 shiftwidth=4: */

// class add_cenario.php: This script registers a new term in the lexicon of the project. 
// Is sent through the URL, a variable $ idProject, which indicates that indicates 
// where the new term should be inserted.

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
    $scenarioExists = checkScenarioExists($_SESSION['id_projeto_corrente'],$title);

    if ($scenarioExists == false)
    {    
        print("<!-- Tentando Inserir Cen&aacute;rio --><BR>");

        $title       = str_replace( ">" , " " , str_replace ( "<" , " " , $title      ) ) ;
        $objective   = str_replace( ">" , " " , str_replace ( "<" , " " , $objective  ) ) ;
        $context     = str_replace( ">" , " " , str_replace ( "<" , " " , $context    ) ) ;
        $actors      = str_replace( ">" , " " , str_replace ( "<" , " " , $actors     ) ) ;
        $resources   = str_replace( ">" , " " , str_replace ( "<" , " " , $resources  ) ) ;
        $exception   = str_replace( ">" , " " , str_replace ( "<" , " " , $exception  ) ) ;
        $episodes     = str_replace( ">" , " " , str_replace ( "<" , " " , $episodes  ) ) ;
        addInsertRequestScenario($_SESSION['id_projeto_corrente'],       
                                      $title,
                                      $objective,
                                      $context,
                                      $actors,
                                      $resources,
                                      $exception,
                                      $episodes,
                                      $_SESSION['id_usuario_corrente']);
     	print("<!-- Cen&aacute;rio Inserido Com Sucesso! --><BR>");
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
		opener.parent.frames['text'].location.replace('main.php?idProject=<?=$_SESSION['id_projeto_corrente']?>');
		//self.close();
		//location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") ); ?>add_cenario.php?idProject=<?=$idProject?>&success=s" ;
	
		location.href = "add_cenario.php?idProject=<?=$idProject?>&success=s";
	</script>
	<?php
} 
else // Script called via the top menu
{
	$projectName = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
	?>
	<html>
            <head>
                    <title>Adicionar Cen&aacute;rio</title>
            </head>
            <body>
                <script language="JavaScript">
                        <!--
                        function blankTest(form)
                        {
                                title = form.title.value;
                                objective = form.objective.value;
                                context = form.context.value;

                                if ((title == ""))
                                { 
                                        alert ("Por favor, digite o titulo do cen\u00e1rio.")
                                        form.title.focus()
                                        return false;
                                } 
                                else
                                {
                                        pattern = /[\\\/\?"<>:|]/;
                                        OK = pattern.exec(title);
                                        if (OK)
                                        {
                                                window.alert ("O t\u00edtulo do cen\u00e1rio n\u00e3o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
                                                form.title.focus();
                                                return false;
                                        } 
                                        else
                                        {
                                            //Nothing to do.
                                        }
                                }

                                if ((objective == ""))
                                {
                                        alert ("Por favor, digite o objetivo do cen\u00e1rio.")
                                    form.objective.focus()
                                    return false;
                                }
                                else
                                {
                                    //Nothing to do.
                                }    

                                if ((context == ""))
                                {
                                        alert ("Por favor, digite o contexto do cen\u00e1rio.")
                                    form.context.focus()
                                    return false;
                                }
                                else
                                {
                                    //Nothing to do.
                                }

                                return true;
                        }
                        //-->

                        <?php

                        // Scenario - insert scenario 

                        // Objective: Allow the user to insert a new scenario
                        // Context: User wants to insert a new scenario
                        // Pre-Conditions: Login, not registered scenario
                        // Actors: User, System
                        // Resources: Data to be registered
                        // Episodes: The system will provide the following fields on the users screen
                        //   - Scenario name.
                        //   - Objective.  Restrictions: Text box with at least 5 visible writing lines.
                        //   - Context.  Restrictions: Text box with at least 5 visible writing lines.
                        //   - Actors.  Restrictions: Text box with at least 5 visible writing lines.
                        //   - Resources.  Restrictions: Text box with at least 5 visible writing lines.
                        //   - Exception.  Restrictions: Text box with at least 5 visible writing lines.
                        //   - Episodes.  Restrictions: Text box with at least 16 visible writing lines.
                        //   - Button to confirm the inclusion of a new scenario.
                        //     Restrictions: After clicking on the confirmation button,
                        //       the system verifies if all fields are filled.
                        // Exception: Se todos os campos n�o foram preenchidos, retorna para o usu�rio uma mensagem avisando
                        //   que todos os campos devem ser preenchidos e um bot�o de voltar para a pagina anterior.

                        ?>
                </SCRIPT>

                <h4>Adicionar Cen&aacute;rio</h4>
                <br>
                <?php
                if ($success == YES)
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
                      <td><input disabled size="51" type="text" value="<?=$projectName?>"></td>
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
                      <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return blankTest(this.form);" value="Adicionar Cen&aacute;rio"></td>
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
