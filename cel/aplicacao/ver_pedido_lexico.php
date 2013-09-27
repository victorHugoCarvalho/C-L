<?php

// ver_pedido_lexico.php: This script shows many lexis requests
// The manager can see the requests already validate.
// The manager can also validate and process requests.
// The manager will have a third option:
// remove a request that is validated or not from request list.
// The manager can answear a request by e-mail directly from this page.
// File that calls: heading.php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");


chkUser("index.php");	// checks whether the user has been authenticated

if (isset($submit)) 
{
	$DB = new PGDB();
	$select = new QUERY($DB);
	$update = new QUERY($DB);
	$delete = new QUERY($DB);
	
	for ($count = 0; $count < sizeof($pedidos); $count++)
	{
 		$update->execute("update pedidolex set aprovado= 1 where id_pedido = $pedidos[$count]") ;
		tratarPedidoLexico($pedidos[$count]);	  				
	}
	
	for($count = 0; $count < sizeof($remover); $count++)
	{
		$delete->execute("delete from pedidolex where id_pedido  = $remover[$count]") ;
		$delete->execute("delete from sinonimo where id_pedidolex = $remover[$count]") ;
	}
	?>
	<script language="javascript1.2">
			opener.parent.frames['code'].location.reload();
			opener.parent.frames['text'].location.replace("main.php") ;
		</script>
	<h4>Operação efetuada com sucesso!</h4>
	<script language="javascript1.2">
		self.close();
		</script>
	<?php
}
else
{
	?>
	<html>
	<head>
	<title>Pedido L&eacute;xico</title>
	</head>
	<body>
	<h2>Pedidos de Altera&ccedil;&atilde;o no L&eacute;xico</h2>
	<form action="?id_projeto=<?=$id_projeto?>" method="post">
	  <?php
	
//      Scenery - Verify change requests of relations
//
//      Purpose: Allow the administrator to manage change requests of relation.
//      Context: Manager wish to view change requests of relation.
//      Precondition: Login, registered project.
//      Actors: Administrator
//      Features: System, database.
//      Episodes: 1- The administrator clicks the option Check requests change of relation.
//      Restriction: Only the Project Manager can have this function visible.
//                2- The system provides the administrator a screen where he can view the
//      history of all pending changes or not for the relations.
//                3- For new requests for the inclusion or modification of relations,
//      the system allows the administrator chooses Approve or Remove. 
//                4- For requests
//      to add or change already approved, the system only enables the option to remove
//                5- To carry selections approval and removal, simply click Process.
	
	$DB = new PGDB();
	$select = new QUERY($DB);
	$select2 = new QUERY($DB);
	$select3 = new QUERY($DB);
	$select->execute("SELECT * FROM pedidolex where id_projeto = $id_projeto") ;
	
	if ($select->getntuples() == 0)
	{
		echo "<BR>Nenhum pedido.<BR>" ;
	}
	else
	{
		$i = 0 ;
		$record = $select->gofirst () ;
		
		while ($record != 'LAST_RECORD_REACHED')
		{
			$id_usuario = $record['id_usuario'];
			$id_pedido = $record['id_pedido'];
			$tipo_pedido = $record['tipo_pedido'];
			$aprovado = $record['aprovado'];
			
			//pega sinonimos
			$select3->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
							
			$select2->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
			$usuario = $select2->gofirst () ;
			
			if (strcasecmp($tipo_pedido,'remover'))
			{
				?>
				<h3>
				O usuário <a  href="mailto:<?=$usuario['email']?>" >
				<?=$usuario['nome']?>
				</a> pede para
				<?=$tipo_pedido?>
				o léxico <font color="#ff0000">
				<?=$record['nome']?>
				</font>
				<?  
				
				if (!strcasecmp($tipo_pedido,'alterar'))
				{
					echo"para léxico abaixo:</h3>" ;
				}
				else
				{
					echo"</h3>" ;
				}
				?>
				<table>
				<td><b>Nome:</b></td>
				<td><?=$record['nome']?></td>
				<tr>
					<td><b>Noção:</b></td>
				    <td><?=$record['nocao']?></td>
				</tr>
				<tr>
					<td><b>Impacto:</b></td>
				    <td><?=$record['impacto']?></td>
				</tr>
				<tr>
					<td><b>Sinônimos:</b></td>
				    <td><?php
				    $sinonimo = $select3->gofirst();
				    $strSinonimos = "";
				    
				    while($sinonimo != 'LAST_RECORD_REACHED')
					{
				    	//echo($sinonimo["nome"] . ", ");
						$strSinonimos = $strSinonimos . $sinonimo["nome"] . ", ";
				        $sinonimo = $select3->gonext();
					}
				
				    echo(substr($strSinonimos, 0, strrpos($strSinonimos, ",")));
				    ?></td>
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
				<h3>O usuário <a  href="mailto:<?=$usuario['email']?>" >
				<?=$usuario['nome']?>
				</a> pede para
				<?=$tipo_pedido?>
				o léxico <font color="#ff0000">
				<?=$record['nome']?>
				</font></h3>
				<?php 
			}
			
			if ($aprovado == 1)
			{
				echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
			}
			else
			{
				echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_pedido\"> <STRONG>Aprovar</STRONG>]<BR>  ";
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
	<i><a href="showSource.php?file=ver_pedido_lexico.php">Veja o c&oacute;digo fonte!</a></i>
	</body>
	</html>
	<?php
}
	?>
