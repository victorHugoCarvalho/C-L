<?php

session_start();

include("funcoes_genericas.php");


chkUser("index.php");        // Cenario: controle de acesso

// Cen�rio - Usu�rio escolhe Projeto

// Objetivo:  Permitir ao Usu�rio escolher um projeto.
// Contexto:  O usu�rio deseja escoher um projeto.
//            Pr�-Condi��es: Login
// Atores:    Usu�rio
// Recursos:  Projetos
// Epis�dios: O Usu�rio seleciona da lista de projetos um projeto da qual ele n�o seja 
//            administrador. 
//            O usu�rio poder�:
//              - Atualizar cen�rio:
//              - Atualizar l�xico.

if( isset( $_GET['id_projeto']))
{
	$id_projeto = $_GET['id_projeto'];
}

?>
<script language="javascript1.3">

function getIDPrj() {
    var select = document.forms[0].id_projeto; // combo-box de projeto
    var indice = select.selectedIndex; // indice selecionado
    var id_projeto = select.options[indice].value; // id_projeto correspondente ao indice
    return id_projeto;

}

function atualizaMenu() {   // carrega o menu correspondente ao projeto
    // Para nao fazer nada se selecionarem o "-- Selecione um Projeto --"
    if (!(document.forms[0].id_projeto.options[0].selected))
    {
          top.frames['code'].location.replace('code.php?id_projeto=' + getIDPrj());
          top.frames['text'].location.replace('main.php?id_projeto=' + getIDPrj());


          location.replace('heading.php?id_projeto=' + getIDPrj());
    } else {

        location.reload();
    }
    return false;
}

<?php
if (isset($id_projeto)) {   // $id_projeto soh nao estara setada caso seja a primeira
                            // vez que o usuario esteja acessando o sistema

    // Checagem de seguranca, pois $id_projeto eh passado atraves de JavaScript (cliente)
    check_proj_perm($_SESSION['id_usuario_corrente'], $id_projeto) or die("Permissao negada");
?>

function setPrjSelected() {
    var select = document.forms[0].id_projeto;
    for (var i = 0; i < select.length; i++) {
        if (select.options[i].value == <?=$id_projeto?>) {
            select.options[i].selected = true;
            i = select.length;
        }
    }
}

<?php
}
?>

function novoCenario() {
 <?php

// Cen�rio - Atualizar Cen�rio 

//Objetivo:	Permitir Inclus�o, Altera��o e Exclus�o de um Cen�rio por um usu�rio
//Contexto:	Usu�rio deseja incluir um cen�rio ainda n�o cadastrado, alterar e/ou excluir
//              um cen�rio previamente cadastrados.
//              Pr�-Condi��o: Login
//Atores:	Usu�rio, Gerente do projeto
//Recursos:	Sistema, menu superior, objeto a ser modificado
//Epis�dios:	O usu�rio clica no menu superior na op��o:
//                Se usu�rio clica em Incluir ent�o INCLUIR CEN�RIO

	if (isset($id_projeto))
    {
    	?>
        var url = 'add_cenario.php?id_projeto=' + '<?=$id_projeto?>';
        <?php
	}
    else
    {
    	?>
        var url = 'add_cenario.php?'
        <?php
	}
	?>
    var where = '_blank';
    var window_spec = 'dependent,height=600,width=550,resizable,scrollbars,titlebar';
    open(url, where, window_spec);
}

function novoLexico() {
 <?php

//Cen�rios -  Atualizar L�xico

//Objetivo:	Permitir Inclus�o, Altera��o e Exclus�o de um L�xico por um usu�rio
//Contexto:	Usu�rio deseja incluir um l�xico ainda n�o cadastrado, alterar e/ou 
//              excluir um cen�rio/l�xico previamente cadastrados.
//              Pr�-Condi��o: Login
//Atores:	Usu�rio, Gerente do projeto
//Recursos:	Sistema, menu superior, objeto a ser modificado
//Epis�dios:	O usu�rio clica no menu superior na op��o:
//                Se usu�rio clica em Incluir ent�o INCLUIR L�XICO

	if (isset($id_projeto))
	{
		?>
		var url = 'add_lexico.php?id_projeto=' + '<?=$id_projeto?>';
		<?php
	}
	else
	{
		?>
		var url = 'add_lexico.php';
		<?php
	}
    ?>

    var where = '_blank';
    var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
    open(url, where, window_spec);
}

function prjInfo(idprojeto) {
    top.frames['text'].location.replace('main.php?id_projeto=' + idprojeto);
}

</script>
<html>
<style>
a {
	font-weight: bolder;
	color: Blue;
	font-family: Verdana, Arial;
	text-decoration: none
}
a:hover {
	font-weight: bolder;
	color: Tomato;
	font-family: Verdana, Arial;
	text-decoration: none
}
</style>
<body bgcolor="#ffffff" text="#000000" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" <?=(isset($id_projeto)) ? "onLoad=\"setPrjSelected();\"" : ""?>>
<form onSubmit="return atualizaMenu();">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr bgcolor="#E0FFFF">
      <td width="294" height="79" ><!--<img src="Images/Logo.jpg"></td>--> 
        <img src="Images/Logo_C.jpg" width="190" height="100"></td>
      <td align="right" valign="top"><table>
          <tr>
            <td align="right" valign="top"><?php 

if (isset($id_projeto))
{
	$id_usuario = $_SESSION['id_usuario_corrente'];
	
	$ret = verificaGerente($id_usuario, $id_projeto);
   	
    if ( $ret != 0 )
	{                                    
		?>
        <font color="#FF0033">Administrador</font>
        <?php
	}
    else
	{
		?>
    	<font color="#FF0033">Usu�rio normal</font>
        <?php
	}
}   
else
{       
	?>
    <?php
}     
?>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Projeto:&nbsp;&nbsp;
<select name="id_projeto" size="1" onChange="atualizaMenu();">
<option>-- Selecione um Projeto --</option>
<?php

// ** Cenario "Login" **
// O sistema d� ao usu�rio a op��o de cadastrar um novo projeto
// ou utilizar um projeto em que ele fa�a parte.

// conecta ao SGBD
$r = bd_connect() or die("Erro ao conectar ao SGBD");

// define a consulta
$q = "SELECT p.id_projeto, p.nome, pa.gerente
      FROM usuario u, participa pa, projeto p
      WHERE u.id_usuario = pa.id_usuario
      AND pa.id_projeto = p.id_projeto
      AND pa.id_usuario = " . $_SESSION["id_usuario_corrente"] . "
      ORDER BY p.nome";

// executa a consulta
$qrr = mysql_query($q) or die("Erro ao executar query");

while ($result = mysql_fetch_array($qrr))    // enquanto houver projetos
{
?>
	<option value="<?=$result['id_projeto']?>">
    <?=($result['gerente'] == 1) ? "*" : ""?>
	<?=$result['nome']?>
    </option>
    <?php
}
?>
              </select>
              &nbsp;&nbsp;
              <input type="submit" value="Atualizar"></td>
          </tr>
          <tr bgcolor="#E0FFFF" height="15">
          <tr bgcolor="#E0FFFF" height="30">
            <td align="right" valign=MIDDLE><?php
if (isset($id_projeto))   	// Se o usuario ja tiver escolhido um projeto,
{							// entao podemos mostrar os links de adicionar cen/lex
                            // e de informacoes (pagina principal) do projeto


// Cen�rio - Administrador escolhe Projeto

// Objetivo:  Permitir ao Administrador escolher um projeto.
// Contexto:  O Administrador deseja escolher um projeto.
//            Pr�-Condi��es: Login, Ser administrador do projeto selecionado.
// Atores:    Administrador
// Recursos:  Projetos doAdministrador
// Epis�dios: Aparecendo no menu as op��es de: 
//            -Adicionar Cen�rio (ver Adicionar Cen�rio); 
//            -Adicionar L�xico (ver Adicionar L�xico); 
//            -Info; 
//            -Adicionar Projeto; 
//            -Alterar Cadastro.


?>
              <a href="#" onClick="novoCenario();">Adicionar Cen�rio</a>&nbsp;&nbsp;&nbsp; <a href="#" onClick="novoLexico();">Adicionar S�mbolo</a>&nbsp;&nbsp;&nbsp; <a href="#" title="Informa��es sobre o Projeto" onClick="prjInfo(<?=$id_projeto?>);">Info</a>&nbsp;&nbsp;&nbsp;
              <?php
}
?>
              <?php

//Cen�rio  -  Cadastrar Novo Projeto 

//Objetivo:    Permitir ao usu�rio cadastrar um novo projeto
//Contexto:    Usu�rio deseja incluir um novo projeto na base de dados
//             Pr�-Condi��o: Login
//Atores:      Usu�rio
//Recursos:    Sistema, dados do projeto, base de dados
//Epis�dios:   O Usu�rio clica na op��o �adicionar projeto� encontrada no menu superior.

?>
              <a href="#" onClick="window.open('add_projeto.php', '_blank', 'dependent,height=313,width=550,resizable,scrollbars,titlebar');">Adicionar 
              Projeto</a>&nbsp;&nbsp;&nbsp;
              <?php


//Cen�rio  -   Remover Novo Projeto 

//Objetivo:    Permitir ao Administrador do projeto remover um projeto
//Contexto:    Um Administrador de projeto deseja remover um determinado projeto da base de dados
//             Pr�-Condi��o: Login, Ser administrador do projeto selecionado.
//Atores:      Administrador
//Recursos:    Sistema, dados do projeto, base de dados
//Epis�dios:   O Administrador clica na op��o �remover projeto� encontrada no menu superior.


if (isset($id_projeto))
{   	
   	$id_usuario = $_SESSION['id_usuario_corrente'];
   	
   	$ret = verificaGerente($id_usuario, $id_projeto);
   	  
	if ( $ret != 0 )
	{
		?>
        <a href="#" onClick="window.open('remove_projeto.php', '_blank', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');">Remover 
        Projeto</a>&nbsp;&nbsp;&nbsp;
        <?php
	}
}       

// Cen�rio - Logar no sistema

// Objetivo:  Permitir ao usu�rio entrar no sistema e escolher um projeto que ele esteja 
//              cadastrado, ou cadastrar novo projeto	
// Contexto:  Sistema est� aberto Usu�rio na tela de login do sistema. 
//            Usu�rio sabe a sua senha Usu�rio deseja entrar no sistema com seu perfil 
//            Pr�-Condi��o: Usu�rio ter acessado ao sistema	
// Atores:	  Usu�rio, Sistema	
// Recursos:  Banco de Dados	
// Epis�dios: O sistema d� ao usu�rio as op��es:
//             - ALTERAR CADASTRO, no qual o usu�rio ter� a possibilidade de realizar 
//               altera��es nos seus dados cadastrais


// Cen�rio - Alterar cadastro
//
//Objetivo:  Permitir ao usu�rio realizar altera��o nos seus dados cadastrais	
//Contexto:  Sistema aberto, Usu�rio ter acessado ao sistema e logado 
//           Usu�rio deseja alterar seus dados cadastrais 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:    Usu�rio, Sistema.	
//Recursos:  Interface	
//Epis�dios: O usu�rio clica na op��o de alterar cadastro da interface

?>
              <a href="#" onClick="window.open('Call_UpdUser.php', '_blank', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');">Alterar 
              Cadastro</a>&nbsp;&nbsp;&nbsp; <a href="mailto:per@les.inf.puc-rio.br">Fale Conosco&nbsp;&nbsp;&nbsp;</a>
              <?php


// Cen�rio - Logar no sistema

// Objetivo:  Permitir ao usu�rio entrar no sistema e escolher um projeto que ele esteja 
//              cadastrado, ou cadastrar novo projeto	
// Contexto:  Sistema est� aberto Usu�rio na tela de login do sistema. 
//            Usu�rio sabe a sua senha Usu�rio deseja entrar no sistema com seu perfil 
//            Pr�-Condi��o: Usu�rio ter acessado ao sistema	
// Atores:    Usu�rio, Sistema	
// Recursos:  Banco de Dados	
// Epis�dios: O sistema d� ao usu�rio as op��es:
//             - REALIZAR LOGOUT, no qual o usu�rio ter� a possibilidade de sair da 
//               sess�o e se logar novamente


// Cen�rio - Realizar logout

// Objetivo:  Permitir ao usu�rio realizar o logout, mantendo a integridade do que foi 
//            realizado,  e retorna a tela de login	
// Contexto:  Sistema aberto. Usu�rio ter acessado ao sistema. 
//            Usu�rio deseja sair da aplica��o e manter a integridade do que foi 
//            realizado 
//            Pr�-Condi��o: Usu�rio ter acessado ao sistema	
// Atores:	  Usu�rio, Sistema.	
// Recursos:  Interface	
// Epis�dios: O usu�rio clica na op��o de logout

?>
              <a href="logout.php" target="_parent");">Sair</a>&nbsp;&nbsp;&nbsp; <a href="ajuda.htm" target="_blank"> Ajuda</a></td>
          </tr>
        </table></td>
    </tr>
    <tr height="33" bgcolor="#00359F" background="Images/FrameTop.gif">
      <td background="Images/TopLeft.gif" width="294" valign="baseline"></td>
      <td background="Images/FrameTop.gif" valign="baseline"></td>
    </tr>
  </table>
</form>
</body>
</html>
