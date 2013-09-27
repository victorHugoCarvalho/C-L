<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// ver_pedido_cenario.php: Esse script exibe os varios pedidos referentes
// ao cenario.O gerente tem a opcao de ver os pedidos
// jah validados. O gerente tb podera validar e processar pedidos.
// O gerente tera uma terceira opcao que eh a de remover o pedido
// validado ou nao da lista de pedidos.O gerente podera responder
// a um pedido via e-mail direto desta pagina.
// Arquivo chamador: heading.php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

// checks whether the user has been authenticated
chkUser("index.php"); 

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
	
	// Cen�rio - Verificar pedidos de altera��o de cen�rios
	
	//Objetivo:	Permitir ao administrador gerenciar os pedidos de altera��o de cen�rios.
	//Contexto:	Gerente deseja visualizar os pedidos de altera��o de cen�rios.
	//Pr�-Condi��o: Login, projeto cadastrado.
	//Atores:	Administrador
	//Recursos:	Sistema, banco de dados.
	//Epis�dios: O administrador clica na op��o de Verificar pedidos de altera��o de cen�rios.
	//           Restri��o: Somente o Administrador do projeto pode ter essa fun��o vis�vel.
	//           O sistema fornece para o administrador uma tela onde poder� visualizar o hist�rico
	//           de todas as altera��es pendentes ou n�o para os cen�rios.
	//           Para novos pedidos de inclus�o ou altera��o de cen�rios,
	//           o sistema permite que o administrador opte por Aprovar ou Remover.
	//           Para os pedidos de inclus�o ou altera��o j� aprovados,
	//           o sistema somente habilita a op��o remover para o administrador.
	//           Para efetivar as sele��es de aprova��o e remo��o, basta clicar em Processar.
	
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
			if ($aprovado == 1)
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
