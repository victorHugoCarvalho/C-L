<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// ver_pedido_cenario.php: This script shows many scenery
// The manager can see the requests already validate.
// The manager can also validate and process requests.
// The manager will have a third option:
// remove a request that is validated or not from request list.
// The manager can answear a request by e-mail directly from this page.
// File that calls: heading.php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

define('APROVADO', '1');

checkUser("index.php"); 	// checks whether the user has been authenticated

if (isset($submit))
{
	$DB = new PGDB();
        $select = new QUERY($DB);
        $update = new QUERY($DB);
        $delete = new QUERY($DB);

	for ($count = 0; $count < sizeof($pedidos); $count++)
	{
                $update->execute("update pedidocen set aprovado= 1 where id_pedido = $pedidos[$count]") ;
		tratarPedidoCenario($pedidos[$count]) ;
	}
	
        for($count = 0; $count < sizeof($remover); $count++)
        {
                $delete->execute("delete from pedidocen where id_pedido = $remover[$count]") ;
	}
	?>
	<script language="javascript1.3">
	
            opener.parent.frames['code'].location.reload();
            opener.parent.frames['text'].location.replace('main.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>');
	
	</script>
	<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>
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
	<title>Pedidos de altera&ccedil;&atilde;o dos Cen&aacute;rios</title>
	</head>
	<body>
	<h2>Pedidos de Altera&ccedil;&atilde;o no Conjunto de Cen&aacute;rios</h2>
	<form action="?id_projeto=<?=$id_projeto?>" method="post">
	  <?php
	
//      Scenery - Verify change requests of scenerys
//
//      Purpose: Allow the administrator to manage change requests of scenerys
//      Context: Manager wish to view change requests of terms of the scenerys
//      Precondition: Login, registered project.
//      Actors: Administrator
//      Features: System, database.
//      Episodes: 1- The administrator clicks the option Check requests change of scenerys.
//      Restriction: Only the Project Manager can have this function visible.
//                2- The system provides the administrator a screen where he can view the
//      history of all pending changes or not for the scenerys.
//                3- For new requests for the inclusion or modification of scenerys,
//      the system allows the administrator chooses Approve or Remove. 
//                4- For requests to add or change already approved, the system only 
//      enables the option to remove
//                5-To carry selections approval and removal, simply click Process.
	
	$DB = new PGDB();
	$select = new QUERY($DB);
	$select2 = new QUERY($DB);
	$select->execute("SELECT * FROM pedidocen WHERE id_projeto = $id_projeto");

	if ($select->getntuples() == 0)
	{
		echo "<BR>Nenhum pedido.<BR>" ;
	}
	else
	{
		$i = 0 ;
                $record = $select->gofirst () ;
	        
                while($record != 'LAST_RECORD_REACHED')
		{
                        $id_usuario = $record['id_usuario'] ;
                        $id_pedido = $record['id_pedido'] ;
                        $tipo_pedido = $record['tipo_pedido'] ;
                        $aprovado = $record['aprovado'] ;
                        $select2->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
                        $usuario = $select2->gofirst () ;
                            
                        if(strcasecmp($tipo_pedido,'remover'))
			{
				?>
			  	<br>
			  	<h3>
			  	O usu&aacute;rio <a  href="mailto:<?=$usuario['email']?>" >
			  	<?=$usuario['nome']?>
			  	</a> pede para
			  	<?=$tipo_pedido?>
			  	o cen&aacute;rio <font color="#ff0000">
			 	<?=$record['titulo']?>
			  	</font>
			  	<?
				if (!strcasecmp($tipo_pedido,'alterar'))
				{
					echo"para cen&aacute;rio abaixo:</h3>";
				}
				else
				{
					echo"</h3>" ;
				}
				?>
				  <table>
				    
				      <td><b>T&iacute;tulo:</b></td>
				      <td><?=$record['titulo']?></td>
				    <tr>
				      <td><b>Objetivo:</b></td>
				      <td><?=$record['objetivo']?></td>
				    </tr>
				    <tr>
				      <td><b>Contexto:</b></td>
				      <td><?=$record['contexto']?></td>
				    </tr>
				    <tr>
				      <td><b>Atores:</b></td>
				      <td><?=$record['atores']?></td>
				    </tr>
				    <tr>
				      <td><b>Recursos:</b></td>
				      <td><?=$record['recursos']?></td>
				    </tr>
				    <tr>
				      <td><b>Exce&ccedil;&atilde;o:</b></td>
				      <td><?=$record['excecao']?></td>
				    </tr>
				    <tr>
				      <td><b>Epis&oacute;dios:</b></td>
				      <td><textarea cols="48" name="episodios" rows="5"><?=$record['episodios']?>
				</textarea></td>
				    </tr>
				    <tr>
				      <td><b>Justificativa:</b></td>
				      <td><textarea name="justificativa" cols="48" rows="2"><?=$record['justificativa']?>
				</textarea></td>
				    </tr>
				  </table>
				  <?php    
			}
			else
			{
				?>
			  	<h3>O usu&aacute;rio <a  href="mailto:<?=$usuario['email']?>" >
			    <?=$usuario['nome']?>
			    </a> pede para
			    <?=$tipo_pedido?>
			    o cen&aacute;rio <font color="#ff0000">
			    <?=$record['titulo']?>
			    </font></h3>
			  	<?php 
			}
			if ($aprovado == APROVADO)
            {
				echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
			} 
			else
            {
				echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_pedido\"> <STRONG>Aprovar</STRONG>]<BR>  ";
				echo "Rejeitar<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\">" ;
            }
	        
                        echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\"> <STRONG>Remover da lista</STRONG>]";
                        print( "<br>\n<hr color=\"#000000\"><br>\n");
                        $record = $select->gonext();
		}
	}
	?>
	<input name="submit" type="submit" value="Processar">
	</form>
            <br>
            <i><a href="showSource.php?file=ver_pedido_cenario.php">Veja o c&oacute;digo fonte!</a></i>
	</body>
	</html>
	<?php
}
	?>
