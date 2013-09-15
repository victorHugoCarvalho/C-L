<?php

session_start();

include("funcoes_genericas.php");

chkUser("index.php");        // checks whether the user has been authenticated

?>

<html>
        <head>
        <script language="javascript1.3">

        // Funcoes que serao usadas quando o script
        // for chamado atraves dele proprio ou da arvore
        function reCarrega(URL) {
            document.location.replace(URL);
        }

        function altCenario(cenario) {
            var url = 'alt_cenario.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_cenario=' + cenario;
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function rmvCenario(cenario) {
            var url = 'rmv_cenario.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_cenario=' + cenario;
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function altLexico(lexico) {
            var url = 'alt_lexico.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_lexico=' + lexico;
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function rmvLexico(lexico) {
            var url = 'rmv_lexico.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_lexico=' + lexico;
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        // Funcoes que serao usadas quando o script
        // for chamado atraves da heading.php
        function pedidoCenario() {
            var url = 'ver_pedido_cenario.php?id_projeto=' + '<?=$id_projeto?>';
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function pedidoLexico() {
            var url = 'ver_pedido_lexico.php?id_projeto=' + '<?=$id_projeto?>';
            var where = '_blank';
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function addUsuario() {
            var url = 'add_usuario.php';
            var where = '_blank';
            var window_spec = 'dependent,height=270,width=490,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function relUsuario() {
            var url = 'rel_usuario.php';
            var where = '_blank';
            var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec);
        }

        function geraXML() {
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

if (isset($id_type) && isset($type)) {      // SCRIPT CHAMADO PELO PROPRIO MAIN.PHP (OU PELA ARVORE)

    if ($type == "c") {
?>
<h3>Informa��es sobre o cen�rio</h3>
<?php
    } else {
?>
<h3>Informa��es sobre o l�xico</h3>
<?php
    }
?>
<table>
          <?php
    $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

    if ($type == "c") {        // se for cenario
        $query = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, episodios
              FROM cenario
              WHERE id_cenario = $id_type";
        $SendQuery = mysql_query($query) or die("Erro ao enviar a query de selecao");
        $result = mysql_fetch_array($SendQuery);
?>
          <tr>
    <td>Titulo:</td>
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
    <td>Epis�dios:</td>
    <td><?=$result['episodios']?></td>
  </tr>
          <tr>
    <td height="40" valign="bottom"><a href="#" onClick="altCenario(<?=$result['id_cenario']?>);">Alterar Cen�rio</a></td>
    <td valign="bottom"><a href="#" onClick="rmvCenario(<?=$result['id_cenario']?>);">Remover Cen�rio</a></td>
  </tr>
          <?php
    } else {
        $query = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_lexico = $id_type";
        $SendQuery = mysql_query($query) or die("Erro ao enviar a query de selecao");
        $result = mysql_fetch_array($SendQuery);
?>
          <tr>
    <td>Nome:</td>
    <td><?=$result['nome']?></td>
  </tr>
          <tr>
    <td>No��o:</td>
    <td><?=$result['nocao']?></td>
  </tr>
          <tr>
    <td>Impacto:</td>
    <td><?=$result['impacto']?></td>
  </tr>
          <tr>
    <td height="40" valign="bottom"><a href="#" onClick="altLexico(<?=$result['id_lexico']?>);">Alterar L�xico</a></td>
    <td valign="bottom"><a href="#" onClick="rmvLexico(<?=$result['id_lexico']?>);">Remover L�xico</a></td>
  </tr>
          <?php
    }
?>
        </table>
<br>
<br>
<br>
<?php
    if ($type == "c") {
?>
<h3>Cen�rios que referenciam este cen�rio</h3>
<?php
    } else {
?>
<h3>Cen�rios e termos do l�xico que referenciam este termo</h3>
<?php
    }

    frame_inferior($connected_SGBD, $type, $id_type);

} elseif (isset($id_projeto)) {         // SCRIPT CHAMADO PELO HEADING.PHP

    // Foi passada uma variavel $id_projeto. Esta variavel deve conter o id de um
    // projeto que o usuario esteja cadastrado. Entretanto, como a passagem eh
    // feita usando JavaScript (no heading.php), devemos checar se este id realmente
    // corresponde a um projeto que o usuario tenha acesso (seguranca).
    check_proj_perm($_SESSION['id_usuario_corrente'], $id_projeto) or die("Permissao negada");

    // Seta uma variavel de sessao correspondente ao projeto atual
    $_SESSION['id_projeto_corrente'] = $id_projeto;
?>
<table>
          <tr>
    <td>Projeto:</td>
    <td><?=simple_query("nome", "projeto", "id_projeto = $id_projeto")?></td>
  </tr>
          <tr>
    <td>Data de cria��o:</td>
    <td><?=simple_query("TO_CHAR(data_criacao, 'DD/MM/YY')", "projeto", "id_projeto = $id_projeto")?></td>
  </tr>
          <tr>
    <td>Descri��o:</td>
    <td><?=simple_query("descricao", "projeto", "id_projeto = $id_projeto")?></td>
  </tr>
        </table>
<?php

    // Verifica se o usuario eh administrador deste projeto
    if (is_admin($_SESSION['id_usuario_corrente'], $id_projeto)) {
?>
<br>
<p><b>Voc� � um administrador deste projeto</b></p>
<p><a href="#" onClick="pedidoCenario();">Verificar pedidos de altera��o de Cen�rios</a></p>
<p><a href="#" onClick="pedidoLexico();">Verificar pedidos de altera�ao de termos do L�xico</a></p>
<p><a href="#" onClick="addUsuario();">Adicionar usu�rios (n�o existente) neste projeto</a></p>
<p><a href="#" onClick="relUsuario();">Relacionar usu�rios j� existentes com este projeto</a></p>
<p><a href="#" onClick="geraXML();">Gerar XML deste projeto</a></p>
<?php
    }
} else {        // SCRIPT CHAMADO PELO INDEX.PHP
?>
<p>Selecione um projeto acima, ou crie um novo projeto.</p>
<?php
}
?>
</body>
</html>
