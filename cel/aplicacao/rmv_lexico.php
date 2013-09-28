<?php
// rmv_lexico.php: Este script faz um pedido de remover um lexico do projeto.
//                 Remove o lexico corrente.
// Arquivo chamador: main.php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

// checks whether the user has been authenticated
chkUser("index.php");        

//Cen�rios -  Excluir L�xico 

//Objetivo:	Permitir ao Usu�rio Excluir uma palavra do l�xico que esteja ativa
//Contexto:	Usu�rio deseja excluir uma palavra do l�xico
//Pr�-Condi��o: Login, palavra do l�xico cadastrada no sistema 
//Atores:	Usu�rio, Sistema
//Recursos:	Dados informados
//Epis�dios:	O sistema fornecer� uma tela para o usu�rio justificar a necessidade
//              daquela exclus�o para que o administrador possa ler e aprovar ou n�o.
//              Esta tela tamb�m conter� um bot�o para a confirma��o da exclus�o.
//              Restri��o: Depois de clicado o bot�o o sistema verifica se todos os campos foram preenchidos 
//Exce��o:	Se todos os campos n�o foram preenchidos, retorna para o usu�rio 
//              uma mensagem avisando que todos os campos devem ser preenchidos 
//              e um bot�o de voltar para a pagina anterior.

inserirPedidoRemoverLexico($id_projeto, $id_lexico, $_SESSION['id_usuario_corrente']);

?>
<script language="javascript1.3">

opener.parent.frames['code'].location.reload();
opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');

</script>

<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>
<script language="javascript1.3">

self.close();

</script> 
