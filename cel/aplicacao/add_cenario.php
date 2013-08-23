<?php

session_start();

/* vim: set expandtab tabstop=4 shiftwidth=4: */

// add_cenario.php: Este script cadastra um novo cenario do projeto. Eh
//                  passada, atraves da URL, uma variavel $id_projeto, que
//                  indica em que projeto deve ser inserido o novo cenario.

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

chkUser("index.php");        // Checa se o usuario foi autenticado

if ( !isset( $sucesso ) )
{
    $sucesso = "n" ;
}

// Conecta ao SGBD
$r = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {
    $ret = checarCenarioExistente($_SESSION['id_projeto_corrente'],$titulo);
    ?>  <!-- ADICIONEI ISTO PARA TESTES -->
        <!--
           RET = <?=$ret?> => RET = <?PHP $ret? print("TRUE") : print("FALSE") ; ?><BR>
       $sucesso        = <?=$sucesso?><BR>
       _GET["sucesso"] = <?=$_GET["sucesso"]?><BR>   
        -->
    <?PHP
    if ($ret == true)
    {    
            print("<!-- Tentando Inserir Cenario --><BR>");

        /* Substitui todas as ocorrencias de ">" e "<" por " " */
        $titulo     = str_replace( ">" , " " , str_replace ( "<" , " " , $titulo     ) ) ;
        $objetivo   = str_replace( ">" , " " , str_replace ( "<" , " " , $objetivo   ) ) ;
        $contexto   = str_replace( ">" , " " , str_replace ( "<" , " " , $contexto   ) ) ;
        $atores     = str_replace( ">" , " " , str_replace ( "<" , " " , $atores     ) ) ;
        $recursos   = str_replace( ">" , " " , str_replace ( "<" , " " , $recursos   ) ) ;
        $excecao    = str_replace( ">" , " " , str_replace ( "<" , " " , $excecao    ) ) ;
        $episodios  = str_replace( ">" , " " , str_replace ( "<" , " " , $episodios  ) ) ;
        inserirPedidoAdicionarCenario($_SESSION['id_projeto_corrente'],       
                                      $titulo,
                                      $objetivo,
                                      $contexto,
                                      $atores,
                                      $recursos,
                                      $excecao,
                                      $episodios,
                                      $_SESSION['id_usuario_corrente']);
            print("<!-- Cenario Inserido Com Sucesso! --><BR>");
     }
     else
     { 
    ?>
    <html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
        <p style="color: red; font-weight: bold; text-align: center">Este cenário já existe!</p>
        <br>
        <br>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
    </body></html>
    <?php
        return;   
     }
?>

<script language="javascript1.2">

opener.parent.frames['code'].location.reload();
opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');
//self.close();
//location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") ); ?>add_cenario.php?id_projeto=<?=$id_projeto?>&sucesso=s" ;


location.href = "add_cenario.php?id_projeto=<?=$id_projeto?>&sucesso=s";

</script>

<?php

} else {    // Script chamado atraves do menu superior
    $nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
?>

<html>
    <head>
        <title>Adicionar Cenário</title>
    </head>
    <body>
<script language="JavaScript">
<!--
function TestarBranco(form)
{
titulo     = form.titulo.value;
objetivo   = form.objetivo.value;
contexto   = form.contexto.value;

  if ((titulo == ""))
    { 
		alert ("Por favor, digite o titulo do cenário.")
		form.titulo.focus()
		return false;
	}else{
		padrao = /[\\\/\?"<>:|]/;
		OK = padrao.exec(titulo);
		if (OK)
		{
			window.alert ("O título do cenário não pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
			form.titulo.focus();
			return false;
		} 
	}
      
  if ((objetivo == ""))
    { alert ("Por favor, digite o objetivo do cenário.")
      form.objetivo.focus()
      return false;}    
      
   if ((contexto == ""))
    { alert ("Por favor, digite o contexto do cenário.")
      form.contexto.focus()
      return false;}        
}
//-->

<?php

// Cenário -  Incluir Cenário 

//Objetivo:        Permitir ao usuário a inclusão de um novo cenário
//Contexto:        Usuário deseja incluir um novo cenário.
//              Pré-Condição: Login, cenário ainda não cadastrado
//Atores:        Usuário, Sistema
//Recursos:        Dados a serem cadastrados
//Episódios:    O sistema fornecerá para o usuário uma tela com os seguintes campos de texto:
//                - Nome Cenário
//                - Objetivo.  Restrição: Caixa de texto com pelo menos 5 linhas de escrita visíveis
//                - Contexto.  Restrição: Caixa de texto com pelo menos 5 linhas de escrita visíveis
//                - Atores.    Restrição: Caixa de texto com pelo menos 5 linhas de escrita visíveis
//                - Recursos.  Restrição: Caixa de texto com pelo menos 5 linhas de escrita visíveis
//                - Exceção.   Restrição: Caixa de texto com pelo menos 5 linhas de escrita visíveis
//                - Episódios. Restrição: Caixa de texto com pelo menos 16 linhas de escrita visíveis
//                - Botão para confirmar a inclusão do novo cenário
//              Restrições: Depois de clicar no botão de confirmação,
//                          o sistema verifica se todos os campos foram preenchidos. 
// Exceção:        Se todos os campos não foram preenchidos, retorna para o usuário uma mensagem avisando
//              que todos os campos devem ser preenchidos e um botão de voltar para a pagina anterior.

?>

</SCRIPT>

        <h4>Adicionar Cenário</h4>
        <br>
<?php
           if ( $sucesso == "s" )
           {
?>
              <p style="color: blue; font-weight: bold; text-align: center">Cenário inserido com sucesso!</p>
<?php    
           }
?>    
        <form action="" method="post">
        <table>
            <tr>
                <td>Projeto:</td>
                <td><input disabled size="51" type="text" value="<?=$nome_projeto?>"></td>
            </tr>
                <td>Título:</td>
                <td><input size="51" name="titulo" type="text" value=""></td>                
            <tr>
                <td>Objetivo:</td>
                <td><textarea cols="51" name="objetivo" rows="3" WRAP="SOFT"></textarea></td>
            </tr>
            <tr>
                <td>Contexto:</td>
                <td><textarea cols="51" name="contexto" rows="3" WRAP="SOFT"></textarea></td>
            </tr>
            <tr>
                <td>Atores:</td>
                <td><textarea cols="51" name="atores" rows="3" WRAP="SOFT"></textarea></td>
            </tr>
            <tr>
                <td>Recursos:</td>
                <td><textarea cols="51" name="recursos" rows="3" WRAP="SOFT"></textarea></td>
            </tr>
            <tr>
                <td>Exceção:</td>
                <td><textarea cols="51" name="excecao" rows="3" WRAP="SOFT"></textarea></td>
            </tr>
            <tr>
                <td>Episódios:</td>
                <td><textarea cols="51" name="episodios" rows="5" WRAP="SOFT"></textarea></td>
            </tr>
            <tr>
                <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return TestarBranco(this.form);" value="Adicionar Cenário"></td>
            </tr>
        </table>
        </form>
        <center><a href="javascript:self.close();">Fechar</a></center>
        <br><i><a href="showSource.php?file=add_cenario.php">Veja o código fonte!</a></i>
    </body>
</html>

<?php
}
?>
