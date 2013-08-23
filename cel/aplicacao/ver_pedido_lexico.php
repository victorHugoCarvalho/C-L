<?php

// ver_pedido_lexico.php: Esse script exibe os varios pedidos referentes
// ao lexico.O gerente tem a opcao de ver os pedidos
//jah validados. O gerente tb podera validar e processar pedidos.
//O gerente tera uma terceira opcao que eh a de remover o pedido
//validado ou nao da lista de pedidos.O gerente podera responder
//a um pedido via e-mail direto desta pagina.
// Arquivo chamador: heading.php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

chkUser("index.php");// Checa se o usuario foi autenticado

if (isset($submit)) {

	$DB = new PGDB () ;
	$select = new QUERY ($DB) ;
	$update = new QUERY ($DB) ;
	$delete = new QUERY ($DB) ;
	for($count = 0; $count < sizeof($pedidos); $count++)
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
} else {
?>
	<html>
	  <head>
	     <title>Pedido Léxico</title>
	  </head>
	<body>
	<h2>Pedidos de Alteração no Léxico</h2>
	<form action="?id_projeto=<?=$id_projeto?>" method="post">

<?php

// Cenário - Verificar pedidos de alteração de termos do léxico

//Objetivo:	Permitir ao administrador gerenciar os pedidos de alteração de termos do léxico.
//Contexto:	Gerente deseja visualizar os pedidos de alteração de termos do léxico.
//              Pré-Condição: Login, projeto cadastrado.
//Atores:	Administrador
//Recursos:	Sistema, banco de dados.
//Episódios: 1- O administrador clica na opção de Verificar pedidos de alteração de termos do léxico.
//           Restrição: Somente o Administrador do projeto pode ter essa função visível.
//           2- O sistema fornece para o administrador uma tela onde poderá visualizar o histórico
//              de todas as alterações pendentes ou não para os termos do léxico.
//           3- Para novos pedidos de inclusão ou alteração de termos do léxico,
//              O sistema permite que o administrador opte por Aprovar ou Remover.
//           4- Para os pedidos de inclusão ou alteração já aprovados,
//              o sistema somente habilita a opção remover para o administrador.
//           5- Para efetivar as seleções de aprovação e remoção, o administrador deve clicar em Processar.

				$DB = new PGDB () ;
				$select = new QUERY ($DB) ;
				$select2 = new QUERY ($DB) ;
				$select3 = new QUERY ($DB) ;
				$select->execute("SELECT * FROM pedidolex where id_projeto = $id_projeto") ;
				if ($select->getntuples() == 0){
			 		 echo "<BR>Nenhum pedido.<BR>" ;
			 }else{
			 		$i = 0 ;
					$record = $select->gofirst () ;
					while($record != 'LAST_RECORD_REACHED'){
						$id_usuario = $record['id_usuario'] ;
						$id_pedido = $record['id_pedido'] ;
						$tipo_pedido = $record['tipo_pedido'] ;
						$aprovado = $record['aprovado'] ;
						
						//pega sinonimos
						$select3->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
						
						$select2->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
						$usuario = $select2->gofirst () ;
						if(strcasecmp($tipo_pedido,'remover')){?>
						<h3>O usuário <a  href="mailto:<?=$usuario['email']?>" ><?=$usuario['nome']?></a> pede para <?=$tipo_pedido?> o léxico <font color="#ff0000"><?=$record['nome']?></font> <?  if(!strcasecmp($tipo_pedido,'alterar')){echo"para léxico abaixo:</h3>" ;}else{echo"</h3>" ;}?>
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
            <td>
            <?php
            $sinonimo = $select3->gofirst();
            $strSinonimos = "";
            while($sinonimo != 'LAST_RECORD_REACHED'){
            //echo($sinonimo["nome"] . ", ");
            $strSinonimos = $strSinonimos . $sinonimo["nome"] . ", ";
            $sinonimo = $select3->gonext();
            }

            echo(substr($strSinonimos, 0, strrpos($strSinonimos, ",")));
            ?>
            </td>
            </tr>
            
            
            <tr>
                <td><b>Justificativa:</b></td>
                <td><textarea name="justificativa" cols="48" rows="2"><?=$record['justificativa']?></textarea></td>
            </tr>
        </table>
					<?php
					}else{?>
							<h3>O usuário <a  href="mailto:<?=$usuario['email']?>" ><?=$usuario['nome']?></a> pede para <?=$tipo_pedido?> o léxico <font color="#ff0000"><?=$record['nome']?></font></h3>
		<?php }
					if ($aprovado == 1)
                    {
					   echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
					} else
                    {
					   echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_pedido\"> <STRONG>Aprovar</STRONG>]<BR>  " ;
//                     echo "Rejeitar<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\">" ;
                    }
                       echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\"> <STRONG>Remover da lista</STRONG>]" ;
                       print( "<br>\n<hr color=\"#000000\"><br>\n") ;
					   $record = $select->gonext () ;
			}
		}?>
			 	 <input name="submit" type="submit" value="Processar">
</form>
<br><i><a href="showSource.php?file=ver_pedido_lexico.php">Veja o código fonte!</a></i>
</body>
</html>

<?php
}
?>


