<?php

session_start();

include("funcoes_genericas.php");
include_once("dataBase/DatabaseIsAdmin.php");


checkUser("index.php");  // checks whether the user has been authenticated      

?>

<html>
        <head>
        <script language="javascript1.3">

        //Functions that will be used when the script 
        //is called through his own or tree
        function reCarrega(URL) 
        {
            document.location.replace(URL);
        }

        function altCenario(cenario) 
        {
            var url = 'alt_cenario.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_cenario=' + cenario;
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function rmvCenario(cenario) 
        {
            var url = 'rmv_cenario.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_cenario=' + cenario;
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function altLexico(lexico) 
        {
            var url = 'alt_lexico.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_lexico=' + lexico;
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function rmvLexico(lexico) 
        {
            var url = 'rmv_lexico.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_lexico=' + lexico;
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

      	//Functions that will be used when the script 
        //Is called through the heading.php
        function pedidoCenario() 
        {
            var url = 'ver_pedido_cenario.php?id_projeto=' + '<?=$id_projeto?>';
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function pedidoLexico() 
        {
            var url = 'ver_pedido_lexico.php?id_projeto=' + '<?=$id_projeto?>';
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function addUsuario() 
        {
            var url = 'add_usuario.php';
            var where = '_blank';
            var window_spec = 'dependent,height=270,width=490,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function relUsuario() 
        {
            var url = 'rel_usuario.php';
            var where = '_blank';
            var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function geraXML() 
        {
            var url = 'xml_gerador.php?id_projeto=' + '<?=$id_projeto?>';
            var where = '_blank';
            var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }
        </script>
        <script type="text/javascript" src="mtmtrack.js">
        </script>
        </head>
        <body>
<?php

include("frame_inferior.php");

$id_type = 0;
$type = 0;

define('SCENERY','c');

//Script called by own main.php (or the tree)
if (isset($id_type) && isset($type)) 
{      

    if ($type == SCENERY) 
	{
?>
<h3>Informa&ccedil;&otilde;es sobre o cen&aacute;rio</h3>
<?php
    } 
    else 
	{
?>
<h3>Informa&ccedil;&otilde;es sobre o l&eacute;xico</h3>
<?php
    }
?>
<table>
          <?php
    $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

    if ($type == SCENERY) 		
	{        
        $query = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, episodios
              FROM cenario
              WHERE id_cenario = $id_type";
        $ExecuteQuery = mysql_query($query) or die("Erro ao enviar a query de selecao");
        $result = mysql_fetch_array($ExecuteQuery);
?>
          <tr>
    <td>T&iacute;tulo:</td>
    <td><?=$result['titulo']?></td>
  </tr>
          <tr>
    <td>Objetivo:</td>
    <td><?=$result['objetivo']?></td>
  </tr>
          <tr>
    <td>Contexto:</td>
    <td><?=$result['contexto']?></td>
  </tr>
          <tr>
    <td>Atores:</td>
    <td><?=$result['atores']?></td>
  </tr>
          <tr>
    <td>Recursos:</td>
    <td><?=$result['recursos']?></td>
  </tr>
          <tr>
    <td>Epis&oacute;dios:</td>
    <td><?=$result['episodios']?></td>
  </tr>
          <tr>
    <td height="40" valign="bottom"><a href="#" onClick="altCenario(<?=$result['id_cenario']?>);">Alterar Cen&aacute;rio</a></td>
    <td valign="bottom"><a href="#" onClick="rmvCenario(<?=$result['id_cenario']?>);">Remover Cen&aacute;rio</a></td>
  </tr>
          <?php
    } 
    else 
	{
        $query = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_lexico = $id_type";
        $ExecuteQuery = mysql_query($query) or die("Erro ao enviar a query de selecao");
        $result = mysql_fetch_array($ExecuteQuery);
?>
          <tr>
    <td>Nome:</td>
    <td><?=$result['nome']?></td>
  </tr>
          <tr>
    <td>No&ccedil;&atilde;o:</td>
    <td><?=$result['nocao']?></td>
  </tr>
          <tr>
    <td>Impacto:</td>
    <td><?=$result['impacto']?></td>
  </tr>
          <tr>
    <td height="40" valign="bottom"><a href="#" onClick="altLexico(<?=$result['id_lexico']?>);">Alterar L&eacute;xico</a></td>
    <td valign="bottom"><a href="#" onClick="rmvLexico(<?=$result['id_lexico']?>);">Remover L&eacute;xico</a></td>
  </tr>
          <?php
    }
?>
        </table>
<br>
<br>
<br>
<?php
    if ($type == SCENERY) 
	{
?>
<h3>Cen&aacute;rios que referenciam este cen&aacute;rio</h3>
<?php
    } 
    else 
	{
?>
<h3>Cen&aacute;rios e termos do l&eacute;xico que referenciam este termo</h3>
<?php
    }

    frame_inferior($connected_SGBD, $type, $id_type);

}
elseif (isset($id_projeto)) 
{         //Script called by heading.php

	//  Was passed a variable $ id_projeto. This variable should contain the id of a
	//  project that the User is registered. However, as the passage is
	//  done using JavaScript (in heading.php), we check if this id really
	//  correspons to a project that the User has access (security).
    check_project_permission($_SESSION['id_usuario_corrente'], $id_projeto) or die("Permissao negada");

    // Set a session variable corresponding to the current project
    $_SESSION['id_projeto_corrente'] = $id_projeto;
?>
<table>
          <tr>
    <td>Projeto:</td>
    <td><?=simple_query("nome", "projeto", "id_projeto = $id_projeto")?></td>
  </tr>
          <tr>
    <td>Data de cria&ccedil;&atilde;o:</td>
    <td><?=simple_query("TO_CHAR(data_criacao, 'DD/MM/YY')", "projeto", "id_projeto = $id_projeto")?></td>
  </tr>
          <tr>
    <td>Descri&ccedil;&atilde;o:</td>
    <td><?=simple_query("descricao", "projeto", "id_projeto = $id_projeto")?></td>
  </tr>
        </table>
<?php

    // Set a session variable corresponding to the current project
    if (is_admin($_SESSION['id_usuario_corrente'], $id_projeto)) 
	{
?>
<br>
<p><b>Voc&ecirc; &eacute; um administrador deste projeto</b></p>
<p><a href="#" onClick="pedidoCenario();">Verificar pedidos de altera&ccedil;&atilde;o de Cen&aacute;rios</a></p>
<p><a href="#" onClick="pedidoLexico();">Verificar pedidos de altera&ccedil;&atilde;ao de termos do L&eacute;xico</a></p>
<p><a href="#" onClick="addUsuario();">Adicionar usu&aacute;rios (n&atilde;o existente) neste projeto</a></p>
<p><a href="#" onClick="relUsuario();">Relacionar usu&aacute;rios j&aacute; existentes com este projeto</a></p>
<p><a href="#" onClick="geraXML();">Gerar XML deste projeto</a></p>
<?php
    }
    else
    {
    	//Nothing to do.
    }
} 
else 
{        //Script called by index.php
?>
<p>Selecione um projeto acima, ou crie um novo projeto.</p>
<?php
}
?>
</body>
</html>
