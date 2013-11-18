<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUser("index.php");

// Conecta ao SGBD
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");


if (isset($submit))   // Script chamado pelo submit
{
    // O procedimento sera remover todos que estao no projeto em questao
    // (menos o administrador que esta adicionando/removendo usuarios)
    // e depois acrescentar aqueles que tiverem sido selecionados
    $query = "DELETE FROM participa
          	  WHERE id_usuario != " . $_SESSION['id_usuario_corrente'] . "
          	  AND id_projeto = " . $_SESSION['id_projeto_corrente'];
    		  mysql_query($query) or die("Erro ao executar a query de DELETE");

    $usuarios_selecionados = count($usuarios);  // Numero de usuarios selecionados no <select>
    
    for ($i = 0; $i < $usuarios_selecionados; $i++) 
    {
    	// Para cada usuario selecionado
        $query = "INSERT INTO participa (id_usuario, id_projeto)
              	  VALUES (" . $usuarios[$i] . ", " . $_SESSION['id_projeto_corrente'] . ")";
        mysql_query($query) or die("Erro ao cadastrar usuario");
    }
?>
<script language="javascript1.3">

self.close();

</script>
<?php
} 
else
{
	?>
	<html>
        <head>
        <title>Selecione os usu&aacute;rios</title>
        <script language="javascript1.3" src="MSelect.js"></script>
        <script language="javascript1.3">

        function createMSelect()
        {
            var usr_lselect = document.forms[0].elements['usuarios[]'];
            var usr_rselect = document.forms[0].usuarios_r;
            var usr_l2r = document.forms[0].usr_l2r;
            var usr_r2l = document.forms[0].usr_r2l;
            var MS_usr = new MSelect(usr_lselect, usr_rselect, usr_l2r, usr_r2l);
        }

        function selAll()
        {
            var usuarios = document.forms[0].elements['usuarios[]'];
            
            for (var i = 0; i < usuarios.length; i++)
			{
    			usuarios.options[i].selected = true;
			}
        }

        </script>
        <style>
	<!--
	select {
		width: 200;
		background-color: #CCFFFF
}
-->
</style>
        </head>
        <body onLoad="createMSelect();">
<h4>Selecione os usu&aacute;rios para participar do projeto "<span style="color: orange">
  <?=simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente'])?>
  </span>":</h4>
<p style="color: red">Mantenha <strong>CTRL</strong> pressionado para selecionar m&uacute;ltiplas op&ccedil;&otilde;es</p>
<form action="" method="post" onSubmit="selAll();">
          <table cellspacing="8" width="100%">
    <tr>
              <td align="center" style="color: green">Participantes:</td>
              <td></td>
              <td></td>
            </tr>
    <tr align="center">
              <td rowspan="2"><select name="usuarios[]" multiple size="6">
                  <?php

// Cen�rio - Relacionar usu�rios ao projeto

// Objetivo:  Permitir ao Administrador relacionar novos usu�rios cadastrados ao projeto selecionado.
// Contexto:  O Administrador deseja relacionar novos usu�rios cadastrados ao projeto selecionado.
//            Pr�-Condi��es: Ser administrador do projeto que deseja relacionar os usu�rios
// Atores:    Administrador
// Recursos:  Usu�rios cadastrados
// Epis�dios: O Administrador clica no link �Relacionar usu�rio j� existentes com este projeto�.

    // Selecionar todos os usuarios que participam deste projeto,
    // menos o administrador que esta executando este script
    $query = "SELECT u.id_usuario, login
          FROM usuario u, participa p
          WHERE u.id_usuario = p.id_usuario
          AND p.id_projeto = " . $_SESSION['id_projeto_corrente'] . "
          AND u.id_usuario != " . $_SESSION['id_usuario_corrente'];

    $sendQuery = mysql_query($query) or die("Erro ao enviar a query");
    while ($result = mysql_fetch_array($sendQuery))
	{
		?>
		<option value="<?=$result['id_usuario']?>">
        <?=$result['login']?>
        </option>
		<?php

		// Cen�rio - Relacionar usu�rios ao projeto
		// Objetivo:  Permitir ao Administrador relacionar novos usu�rios cadastrados ao projeto selecionado.
		// Contexto:  O Administrador deseja relacionar novos usu�rios cadastrados ao projeto selecionado.
		//            Pr�-Condi��es: Ser administrador do projeto que deseja relacionar os usu�rios
		// Atores:    Administrador
		// Recursos:  Usu�rios cadastrados
		// Epis�dios: Excluindo usu�rio(s) do projeto: o administrador seleciona os usu�rios cadastrados 
		//            (j� existentes) da lista Participantes (usu�rios que pertencem a este projeto) 
		//            e clica no bot�o -> . 
		?>
		<?php
    }
    
	?>
    </select></td>
    <td><input name="usr_l2r" type="button" value="->"></td>
    <td rowspan="2"><select  multiple name="usuarios_r" size="6">
	<?php
                  
    // Selecionar todos os usuarios que nao participam deste projeto
    $subQuery = "SELECT id_usuario FROM participa where participa.id_projeto =".$_SESSION['id_projeto_corrente'];
    $sendSubQuery = mysql_query($subQuery) or die("Erro ao enviar a subquery");
    $resultadosubquery = "(0)";
    
    if($sendSubQuery != 0)
    {
    	$row = mysql_fetch_row($sendSubQuery);
    	$resultadosubquery = "( $row[0]";	
    						while($row = mysql_fetch_row($sendSubQuery))
    						{
    							$resultadosubquery = "$resultadosubquery , $row[0]";
    						}
    						$resultadosubquery = "$resultadosubquery )";
    }
    else
    {
    	//Nothing to do.
    }
    
    $query = "SELECT usuario.id_usuario, usuario.login FROM usuario where usuario.id_usuario not in ".$resultadosubq;
    //$query = "SELECT usuario.id_usuario, usuario.login FROM usuario, participa where usuario.id_usuario=participa.id_usuario and participa.id_projeto<>".$_SESSION['id_projeto_corrente']." and participa.id_usuario not in ".$resultadosubq;

    echo($query);
    $send_query = mysql_query($query) or die("Erro ao enviar a query");
    
    while ($result = mysql_fetch_array($send_query))
	{
		?>
        <option value="<?=$result['id_usuario']?>">
        <?=$result['login']?>
        </option>
        <?php

		// Cen�rio - Relacionar usu�rios ao projeto
		
		// Objetivo:  Permitir ao Administrador relacionar novos usu�rios cadastrados ao projeto selecionado.
		// Contexto:  O Administrador deseja relacionar novos usu�rios cadastrados ao projeto selecionado.
		//            Pr�-Condi��es: Ser administrador do projeto que deseja relacionar os usu�rios
		// Atores:    Administrador
		// Recursos:  Usu�rios cadastrados
		// Epis�dios: Incluindo usu�rio(s) ao projeto: o administrador seleciona os usu�rios cadastrados 
		//           (j� existentes) da lista de usu�rios que n�o pertencem a este projeto e 
		//           clica no bot�o <- . 
		
		?>
		<?php
    }
    
	?>
	</select></td>
	</tr>
    <tr align="center">
    <td><input name="usr_r2l" type="button" value="<-"></td>
    </tr>
    <?php

	// Cen�rio - Relacionar usu�rios ao projeto
	
	// Objetivo:  Permitir ao Administrador relacionar novos usu�rios cadastrados ao projeto selecionado.
	// Contexto:  O Administrador deseja relacionar novos usu�rios cadastrados ao projeto selecionado.
	//            Pr�-Condi��es: Ser administrador do projeto que deseja relacionar os usu�rios
	// Atores:    Administrador
	// Recursos:  Usu�rios cadastrados
	// Epis�dios: Para atualizar os relacionamentos realizados, o administrador clica no bot�o Atualizar.
	
	?>
	<tr>
	<td align="center" colspan="3" height="50" valign="bottom"><input name="submit" type="submit" value="Atualizar"></td>
	</tr>
	</table>
	</form>
	<br>
	<i><a href="showSource.php?file=rel_usuario.php">Veja o c&oacute;digo fonte!</a></i>
	</body>
	</html>
	<?php
}
	?>
