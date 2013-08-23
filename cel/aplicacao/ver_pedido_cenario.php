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

chkUser("index.php"); // Checa se o usuario foi autenticado
if (isset($submit)) {
        $DB = new PGDB () ;
        $select = new QUERY ($DB) ;
        $update = new QUERY ($DB) ;
        $delete = new QUERY ($DB) ;
        for($count = 0; $count < sizeof($pedidos); $count++)
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

<h4>Operação efetuada com sucesso!</h4>
<script language="javascript1.3">

self.close();

</script>

<?php
} else {?>
<html>
  <head>
     <title>Pedidos de alteração dos Cenários</title>
  </head>
<body>
<h2>Pedidos de Alteração no Conjunto de Cenários</h2>
<form action="?id_projeto=<?=$id_projeto?>" method="post">

<?php

// Cenário - Verificar pedidos de alteração de cenários

//Objetivo:	Permitir ao administrador gerenciar os pedidos de alteração de cenários.
//Contexto:	Gerente deseja visualizar os pedidos de alteração de cenários.
//              Pré-Condição: Login, projeto cadastrado.
//Atores:	Administrador
//Recursos:	Sistema, banco de dados.
//Episódios: O administrador clica na opção de Verificar pedidos de alteração de cenários.
//           Restrição: Somente o Administrador do projeto pode ter essa função visível.
//           O sistema fornece para o administrador uma tela onde poderá visualizar o histórico
//           de todas as alterações pendentes ou não para os cenários.
//           Para novos pedidos de inclusão ou alteração de cenários,
//           o sistema permite que o administrador opte por Aprovar ou Remover.
//           Para os pedidos de inclusão ou alteração já aprovados,
//           o sistema somente habilita a opção remover para o administrador.
//           Para efetivar as seleções de aprovação e remoção, basta clicar em Processar.

                $DB = new PGDB () ;
                $select = new QUERY ($DB) ;
                $select2 = new QUERY ($DB) ;
                $select->execute("SELECT * FROM pedidocen WHERE id_projeto = $id_projeto") ;
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
                            $select2->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
                            $usuario = $select2->gofirst () ;
                            if(strcasecmp($tipo_pedido,'remover')){?>
        
        <br>
                <h3>O usuário <a  href="mailto:<?=$usuario['email']?>" ><?=$usuario['nome']?></a> pede para <?=$tipo_pedido?> o cenário <font color="#ff0000"><?=$record['titulo']?></font> <?  if(!strcasecmp($tipo_pedido,'alterar')){echo"para cenário abaixo:</h3>" ;}else{echo"</h3>" ;}?>
                    <table>
                <td><b>Título:</b></td>
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
                <td><b>Exceção:</b></td>
                <td><?=$record['excecao']?></td>
            </tr>
            <tr>
                <td><b>Episódios:</b></td>
                <td><textarea cols="48" name="episodios" rows="5"><?=$record['episodios']?></textarea></td>
            </tr>
            <tr>
                <td><b>Justificativa:</b></td>
                <td><textarea name="justificativa" cols="48" rows="2"><?=$record['justificativa']?></textarea></td>
            </tr>
        </table>
<?php    }else{?>
            <h3>O usuário <a  href="mailto:<?=$usuario['email']?>" ><?=$usuario['nome']?></a> pede para <?=$tipo_pedido?> o cenário <font color="#ff0000"><?=$record['titulo']?></font></h3>
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
    }
?>
<input name="submit" type="submit" value="Processar">
</form>
<br><i><a href="showSource.php?file=ver_pedido_cenario.php">Veja o código fonte!</a></i>
</body>
</html>
<?php
}
?>

