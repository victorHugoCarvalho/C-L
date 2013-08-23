<?php

session_start();
include_once("CELConfig/CELConfig.inc");

//$_SESSION['site'] = 'http://pes.inf.puc-rio.br/pes03_1_1/Site/desenvolvimento/teste/';       
//$_SESSION['site'] = 'http://139.82.24.189/cel_vf/aplicacao/teste/';
/* URL do diretorio contendo os arquivos de DAML */
$_SESSION['site'] = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL") ;

//$_SESSION['diretorio'] = "/home/local/pes/pes03_1_1/Site/desenvolvimento/teste/";        
//$_SESSION['diretorio'] = "teste/";        
/* Caminho relativo ao CEL do diretorio contendo os arquivos de DAML */
$_SESSION['diretorio'] = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL") ;

include("funcoes_genericas.php");    
include("httprequest.inc");
include_once("coloca_links.php");


// Checa se o usuário foi autenticado
chkUser("index.php");   

//Recebe parametro da heading.php. Sem isso vai travar ja que a variavel nao foi inicializada 
if( isset( $_GET['id_projeto']))    
{    
    $id_projeto = $_GET['id_projeto'];    
}    
else    
{    
  // $id_projeto = ""; 
}    

if (!isset  ( $_SESSION['id_projeto_corrente'] ))    
{    

   $_SESSION['id_projeto_corrente'] = "";    
}    


?>    

<html> 



    <head> 
        <LINK rel="stylesheet" type="text/css" href="style.css"> 
        <script language="javascript1.3"> 

        // Funcoes que serao usadas quando o script for chamado atraves dele proprio ou da arvore 
        function reCarrega(URL) { 
            document.location.replace(URL); 
        } 

<?php    

// Cenário - Atualizar Cenário 

//Objetivo:    Permitir Inclusão, Alteração e Exclusão de um Cenário por um usuário 
//Contexto:    Usuário deseja incluir um cenário ainda não cadastrado, alterar e/ou excluir 
//              um cenário previamente cadastrados. 
//              Pré-Condição: Login 
//Atores:    Usuário, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Episódios:    O usuário clica no menu superior na opção: 
//                Se usuário clica em Alterar então ALTERAR CENÁRIO 

?>    

        function altCenario(cenario) { 
            var url = 'alt_cenario.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_cenario=' + cenario; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=660,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec); 
        } 

<?php    

// Cenário - Atualizar Cenário 

//Objetivo:    Permitir Inclusão, Alteração e Exclusão de um Cenário por um usuário 
//Contexto:    Usuário deseja incluir um cenário ainda não cadastrado, alterar e/ou excluir 
//              um cenário previamente cadastrados. 
//              Pré-Condição: Login 
//Atores:    Usuário, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Episódios:    O usuário clica no menu superior na opção: 
//                Se usuário clica em Excluir então EXCLUIR CENÁRIO 

?>    

        function rmvCenario(cenario) { 
            var url = 'rmv_cenario.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_cenario=' + cenario; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

//Cenários -  Atualizar Léxico 

//Objetivo:    Permitir Inclusão, Alteração e Exclusão de um Léxico por um usuário 
//Contexto:    Usuário deseja incluir um lexico ainda não cadastrado, alterar e/ou 
//              excluir um cenário/léxico previamente cadastrados. 
//              Pré-Condição: Login 
//Atores:    Usuário, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Episódios:    O usuário clica no menu superior na opção: 
//                Se usuário clica em Alterar então ALTERAR LÉXICO 

?>    

        function altLexico(lexico) { 
            var url = 'alt_lexico.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_lexico=' + lexico; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
            open(url, where, window_spec); 
        } 

<?php    

//Cenários -  Atualizar Léxico 

//Objetivo:    Permitir Inclusão, Alteração e Exclusão de um Léxico por um usuário 
//Contexto:    Usuário deseja incluir um lexico ainda não cadastrado, alterar e/ou 
//              excluir um cenário/léxico previamente cadastrados. 
//              Pré-Condição: Login 
//Atores:    Usuário, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Episódios:    O usuário clica no menu superior na opção: 
//                Se usuário clica em Excluir então EXCLUIR LÉXICO 

?>    

        function rmvLexico(lexico) { 
            var url = 'rmv_lexico.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_lexico=' + lexico; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

        // Funcoes que serao usadas quando o script 
        // for chamado atraves da heading.php 

<?php    

// Cenário - Atualizar Cenário 

//Objetivo:    Permitir Inclusão, Alteração e Exclusão de um Cenário por um usuário 
//Contexto:    Usuário deseja incluir um cenário ainda não cadastrado, alterar e/ou excluir 
//              um cenário previamente cadastrados. 
//              Pré-Condição: Login 
//Atores:    Usuário, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Episódios:    O usuário clica no menu superior na opção: 
//                Se usuário clica em Alterar então ALTERAR CENÁRIO 

?>    

        function altConceito(conceito) { 
            var url = 'alt_conceito.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_conceito=' + conceito; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

// Cenário - Atualizar Conceito

//Objetivo:    Permitir Inclusão, Alteração e Exclusão de um Cenário por um usuário 
//Contexto:    Usuário deseja incluir um cenário ainda não cadastrado, alterar e/ou excluir 
//              um cenário previamente cadastrados. 
//              Pré-Condição: Login 
//Atores:    Usuário, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Episódios:    O usuário clica no menu superior na opção: 
//                Se usuário clica em Excluir então EXCLUIR CENÁRIO 

?>    

        function rmvConceito(conceito) { 
            var url = 'rmv_conceito.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_conceito=' + conceito; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 
        
        function rmvRelacao(relacao) { 
            
            var url = 'rmv_relacao.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_relacao=' + relacao; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        }

<?php    


// Cenário - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
//            Pré-Condições: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Episódios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as opções de: 
//            -Verificar pedidos de alteração de cenário (ver Verificar pedidos de alteração 
//            de cenário); 

?>    

        function pedidoCenario() { 
            <?php    
             if (isset($id_projeto))    
             {    
             ?>    
				var url = 'ver_pedido_cenario.php?id_projeto=' + '<?=$id_projeto?>'; 
             <?php    
             }    
             else    
             {    
             ?>    
				var url = 'ver_pedido_cenario.php'; 
             <?php    
             }    

            ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

// Cenário - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
//            Pré-Condições: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Episódios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as opções de: 
//            - Verificar pedidos de alteração de termos do léxico 
//            ( ver Verificar pedidos de alteração de termos do léxico); 

?>    

        function pedidoLexico() { 

         <?php    
                     if (isset($id_projeto))    
                     {    
                     ?>    
						var url = 'ver_pedido_lexico.php?id_projeto=' + '<?=$id_projeto?>'; 
                     <?php    
                     }    
                     else    
                     {    
                     ?>    
						var url = 'ver_pedido_lexico.php?' 
                     <?php    
                     }    

            ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

// Cenário - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
//            Pré-Condições: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Episódios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as opções de: 
//            - Verificar pedidos de alteração de termos do léxico 
//            ( ver Verificar pedidos de alteração de termos do léxico); 

?>    

        function pedidoConceito() { 

         <?php    
                     if (isset($id_projeto))    
                     {    
                     ?>    
						var url = 'ver_pedido_conceito.php?id_projeto=' + '<?=$id_projeto?>'; 
                     <?php    
                     }    
                     else    
                     {    
                     ?>    
						var url = 'ver_pedido_conceito.php?' 
                     <?php    
                     }    

            ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 
        
        function pedidoRelacao() { 

         <?php    
                     if (isset($id_projeto))    
                     {    
                     ?>    
						var url = 'ver_pedido_relacao.php?id_projeto=' + '<?=$id_projeto?>'; 
                     <?php    
                     }    
                     else    
                     {    
                     ?>    
						var url = 'ver_pedido_relacao.php?' 
                     <?php    
                     }    

            ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php   

// Cenário - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
//            Pré-Condições: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Episódios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as opções de: 
//            -Adicionar usuário (não existente) neste projeto (ver Adicionar Usuário); 

?>    

        function addUsuario() { 
            var url = 'add_usuario.php'; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=320,width=490,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

// Cenário - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
//            Pré-Condições: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Episódios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as opções de: 
//            -Relacionar usuários já existentes com este projeto 
//            (ver Relacionar usuários com projetos); 

?>    

        function relUsuario() { 
            var url = 'rel_usuario.php'; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=380,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

// Cenário - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
//            Pré-Condições: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Episódios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as opções de: 
//            -Gerar xml deste projeto (ver Gerar relatórios XML); 

?>    

        function geraXML() 
        { 

        <?php    
                             if (isset($id_projeto))    
                             {    
                             ?>    
								var url = 'form_xml.php?id_projeto=' + '<?=$id_projeto?>'; 
                             <?php    
                             }    
                             else    
                             {    
                             ?>    
								var url = 'form_xml.php?' 
                             <?php    
                             }    

            ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

        function recuperaXML() 
                { 

        <?php    
                             if (isset($id_projeto))    
                             {    
                             ?>    
								var url = 'recuperarXML.php?id_projeto=' + '<?=$id_projeto?>'; 
                             <?php    
                             }    
                             else    
                             {    
                             ?>    
								var url = 'recuperarXML.php?' 
                             <?php    
                             }    

            ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 
		
		function geraGrafo() 
        { 

        <?php    
			if (isset($id_projeto))    
            {    
                ?>    
					var url = 'gerarGrafo.php?id_projeto=' + '<?=$id_projeto?>'; 
                <?php    
            }else    
            {    
                ?>    
					var url = 'gerarGrafo.php?' 
                <?php    
            }    

        ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

		
		<?php   

		// Ontologia 

		// Objetivo:  Gerar ontologia do projeto 
		
		?>    
        function geraOntologia() 
        { 

        <?php    
                             if (isset($id_projeto))    
                             {    
                             ?>    
								var url = 'inicio.php?id_projeto=' + '<?=$id_projeto?>'; 
                             <?php    
                             }    
                             else    
                             {    
                             ?>    
								var url = 'inicio.php?' 
                             <?php    
                             }    

            ?>    

            var where = '_blank'; 
            var window_spec = ""; 
            open(url, where, window_spec); 
        } 

<?php   

// Ontologia - DAML 

// Objetivo:  Gerar daml deste da ontologia do projeto 
?>    
        function geraDAML() 
        { 

        <?php    
                             if (isset($id_projeto))    
                             {    
                             ?>    
								var url = 'form_daml.php?id_projeto=' + '<?=$id_projeto?>'; 
                             <?php    
                             }    
                             else    
                             {    
                             ?>    
								var url = 'form_daml.php?' 
                             <?php    
                             }    

            ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=375,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php   

// Objetivo: Recuperar histórico da ontologia em DAML 
?>    
        function recuperaDAML() 
        { 

        <?php    
                             if (isset($id_projeto))    
                             {    
                             ?>    
								var url = 'recuperaDAML.php?id_projeto=' + '<?=$id_projeto?>'; 
                             <?php    
                             }    
                             else    
                             {    
                             ?>    
								var url = 'recuperaDAML.php?' 
                             <?php    
                             }    

            ?>    

            var where = '_blank'; 
            var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 


        </script> 
        <script type="text/javascript" src="mtmtrack.js"> 
        </script> 
    </head> 
    <body> 

<!--                     PRIMEIRA PARTE                                     --> 

<?php    

include("frame_inferior.php");    


if (isset($id) && isset($t)) {      // SCRIPT CHAMADO PELO PROPRIO MAIN.PHP (OU PELA ARVORE) 
    $vetorVazio = array();
    if ($t == "c")        { print "<h3>Informações sobre o cenário</h3>";   

    } elseif ($t == "l")  { print "<h3>Informações sobre o símbolo</h3>";   

    } elseif ($t == "oc") { print "<h3>Informações sobre o conceito</h3>";    

    } elseif ($t == "or") { print "<h3>Informações sobre a relação</h3>";   

    } elseif ($t == "oa") { print "<h3>Informações sobre o axioma</h3>";   

    }    

?>    
        <table> 




<!--                     SEGUNDA PARTE                                     --> 


<?php    
    $c = bd_connect() or die("Erro ao conectar ao SGBD");    
?>   



<!-- CENÁRIO --> 

<?php   
    
	if ($t == "c") {        // se for cenario 
        
		$q = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_projeto    
              FROM cenario    
              WHERE id_cenario = $id";    
        
		$qrr = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());    
        $result = mysql_fetch_array($qrr);  
        
		$c_id_projeto = $result['id_projeto'];
		
		$vetorDeCenarios = carrega_vetor_cenario( $c_id_projeto, $id, true ); // carrega vetor de cenario
        quicksort( $vetorDeCenarios, 0, count($vetorDeCenarios)-1,'cenario' );
      
	    $vetorDeLexicos = carrega_vetor_lexicos( $c_id_projeto, 0, false ); // carrega vetor de léxicos 
        quicksort( $vetorDeLexicos, 0, count($vetorDeLexicos)-1,'lexico' );
    		
?>    

            <tr> 
                <th>Titulo:</th><td CLASS="Estilo">
        <?php echo nl2br(monta_links( $result['titulo'], $vetorDeLexicos, $vetorVazio)) ;?>
                </td> 

            </tr> 
            <tr> 
                <th>Objetivo:</th><td CLASS="Estilo">
		<?php echo nl2br(monta_links( $result['objetivo'], $vetorDeLexicos, $vetorVazio )) ; ?>
				</td> 
            </tr> 
            <tr> 
                <th>Contexto:</th><td CLASS="Estilo">
		<?php
    	    echo nl2br(monta_links( $result['contexto'], $vetorDeLexicos, $vetorDeCenarios ) ); ?>		 
				</td> 
            </tr> 
            <tr> 
                <th>Atores:</th><td CLASS="Estilo">
		<?php echo nl2br(monta_links( $result['atores'], $vetorDeLexicos, $vetorVazio) ) ; ?>
                </td>  
            </tr> 
            <tr> 
                <th>Recursos:</th><td CLASS="Estilo">
		<?php echo nl2br(monta_links( $result['recursos'], $vetorDeLexicos, $vetorVazio ) ) ; ?>
                </td> 
            </tr> 
            <tr> 
                <th>Exceção:</th><td CLASS="Estilo">
		<?php echo nl2br(monta_links( $result['excecao'], $vetorDeLexicos, $vetorVazio) ) ; ?>
                </td> 
            </tr> 
            <tr> 
                <th>Episódios:</th><td CLASS="Estilo">
		<?php 
	  		echo nl2br(monta_links( $result['episodios'], $vetorDeLexicos, $vetorDeCenarios ) ); ?>
	  	
                </td> 
            </tr> 
        </TABLE> 
        <BR> 
        <TABLE> 
            <tr> 
                 <td CLASS="Estilo" height="40" valign=MIDDLE> 
                    <a href="#" onClick="altCenario(<?=$result['id_cenario']?>);">Alterar Cenário</a> 
                </th> 
                <td CLASS="Estilo"  valign=MIDDLE> 
                    <a href="#" onClick="rmvCenario(<?=$result['id_cenario']?>);">Remover Cenário</a> 
                </th> 
            </tr> 


<!-- LÉXICO --> 

<?php    
    } elseif ($t == "l") {
              
        $q = "SELECT id_lexico, nome, nocao, impacto, tipo, id_projeto    
              FROM lexico    
              WHERE id_lexico = $id";    
      
		$qrr = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());    
        $result = mysql_fetch_array($qrr);
        
        $l_id_projeto = $result['id_projeto'];
        
        $vetorDeLexicos = carrega_vetor_lexicos( $l_id_projeto, $id, true );  
		
        quicksort( $vetorDeLexicos, 0, count( $vetorDeLexicos )-1,'lexico' );
 
?>    
            <tr> 
                <th>Nome:</th><td CLASS="Estilo"><?php echo $result['nome']; ?>
				</td> 
            </tr> 
            <tr> 
                <th>Noção:</th><td CLASS="Estilo"><?php echo nl2br( monta_links( $result['nocao'], $vetorDeLexicos, $vetorVazio ) ); ?>
				</td> 
            </tr> 
            <tr> 
                <th>Classificação:</th><td CLASS="Estilo"><?=nl2br( $result['tipo'] ) ?>
				</td> 
            </tr> 
            <tr> 
                <th>Impacto(s):</th><td CLASS="Estilo"><?php echo nl2br( monta_links( $result['impacto'], $vetorDeLexicos, $vetorVazio ) ); ?> 
				</td>
            </tr> 
            <tr> 
            <th>Sinônimo(s):</th> 

			<?php //sinonimos 
                 $id_projeto = $_SESSION['id_projeto_corrente'];    
                 $qSinonimo = "SELECT * FROM sinonimo WHERE id_lexico = $id";    
                 $qrr = mysql_query($qSinonimo) or die("Erro ao enviar a query de Sinonimos". mysql_error());    

                 $tempS = array();
                 
                 while ($resultSinonimo = mysql_fetch_array($qrr))    
                 {    
                      $tempS[] = $resultSinonimo['nome'];    
                 }    

			?>    
               
			   <td CLASS="Estilo">
			
			<?php                    
                $count = count($tempS);
                
                 for ($i = 0; $i < $count; $i++)    
                 {    
                      if ($i == $count-1)
                      {    
                          echo $tempS[$i].".";
                      }
                      else
                      {
                      	  echo $tempS[$i].", ";
                      }
                 }    

			 ?>    

            </td> 

            </tr> 
        </TABLE> 
        <BR> 
        <TABLE> 
            <tr> 
                <td CLASS="Estilo" height="40" valign="middle"> 
                    <a href="#" onClick="altLexico(<?=$result['id_lexico']?>);">Alterar Símbolo</a> 
                </th> 
                <td CLASS="Estilo" valign="middle"> 
                    <a href="#" onClick="rmvLexico(<?=$result['id_lexico']?>);">Remover Símbolo</a> 
                </th> 
            </tr> 


<!-- ONTOLOGIA - CONCEITO --> 

<?php    
    } elseif ($t == "oc") {        // se for cenario 
        
		$q = "SELECT id_conceito, nome, descricao   
              FROM   conceito   
              WHERE  id_conceito = $id";    
        
		$qrr = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());    
        $result = mysql_fetch_array($qrr);    
?>    

            <tr> 
                <th>Nome:</th><td CLASS="Estilo"><?=$result['nome']?></td> 
            </tr> 
            <tr> 
                <th>Descrição:</th><td CLASS="Estilo"><?=nl2br($result['descricao'])?></td> 
            </tr> 
        </TABLE> 
        <BR> 
        <TABLE> 
            <tr> 
                <td CLASS="Estilo" height="40" valign=MIDDLE>                     
                </th> 
                <td CLASS="Estilo"  valign=MIDDLE> 
                    <a href="#" onClick="rmvConceito(<?=$result['id_conceito']?>);">Remover Conceito</a> 
                </th> 
            </tr> 




<!-- ONTOLOGIA - RELAÇÕES --> 

<?php    
    } elseif ($t == "or") {        // se for cenario 
        $q = "SELECT id_relacao, nome   
              FROM relacao   
              WHERE id_relacao = $id";    
        $qrr = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());    
        $result = mysql_fetch_array($qrr);    
?>    

            <tr> 
                <th>Nome:</th><td CLASS="Estilo"><?=$result['nome']?></td> 
            </tr> 

        </TABLE> 
        <BR> 
        <TABLE> 
            <tr> 
                 <td CLASS="Estilo" height="40" valign=MIDDLE>                   
                </th>
                <td CLASS="Estilo"  valign=MIDDLE> 
                    <a href="#" onClick="rmvRelacao(<?=$result['id_relacao']?>);">Remover Relação</a> 
                </th> 
            </tr> 




<?php    
    }    
?>   

        </table> 
        <br> 


<!--                     TERCEIRA PARTE                                     --> 


<?php    
    if ($t == "c")       { print "<h3>Cenários que referenciam este cenário</h3>";   

    } elseif ($t == "l") { print "<h3>Cenários e termos do léxico que referenciam este termo</h3>";   

    } elseif ($t == "oc") { print "<h3>Relações do conceito</h3>";   

    } elseif ($t == "or") { print "<h3>Conceitos referentes à relação</h3>";   

    } elseif ($t == "oa") { print "<h3>Axioma</h3>";   

    }    
?>   





<!--                     QUARTA PARTE                                     --> 


<?php   

    frame_inferior($c, $t, $id);    

} elseif (isset($id_projeto)) {         // SCRIPT CHAMADO PELO HEADING.PHP 

    // Foi passada uma variavel $id_projeto. Esta variavel deve conter o id de um 
    // projeto que o usuario esteja cadastrado. Entretanto, como a passagem eh 
    // feita usando JavaScript (no heading.php), devemos checar se este id realmente 
    // corresponde a um projeto que o usuario tenha acesso (seguranca). 
    check_proj_perm($_SESSION['id_usuario_corrente'], $id_projeto) or die("Permissao negada");    

    // Seta uma variavel de sessao correspondente ao projeto atual 
    $_SESSION['id_projeto_corrente'] = $id_projeto;    
?>    

        <table ALIGN=CENTER> 
            <tr> 
                <th>Projeto:</th> 
                <td CLASS="Estilo"><?=simple_query("nome", "projeto", "id_projeto = $id_projeto")?></td> 
            </tr> 
            <tr> 
                <th>Data de criação:</th> 
                <?php    
                    $data = simple_query("data_criacao", "projeto", "id_projeto = $id_projeto");    
                ?>    

        <td CLASS="Estilo"><?=formataData($data)?></td> 

            </tr> 
            <tr> 
                <th>Descrição:</th> 
                <td CLASS="Estilo"><?=nl2br(simple_query("descricao", "projeto", "id_projeto = $id_projeto"))?></td> 
            </tr> 
        </table> 

<?php    

// Cenário - Escolher Projeto 

// Objetivo:  Permitir ao Administrador/Usuário escolher um projeto. 
// Contexto:  O Administrador/Usuário deseja escolher um projeto. 
//            Pré-Condições: Login, Ser Administrador 
// Atores:    Administrador, Usuário 
// Recursos:  Usuários cadastrados 
// Episódios: Caso o Usuario selecione da lista de projetos um projeto da qual ele seja 
//            administrador, ver Administrador escolhe Projeto. 
//            Caso contrário, ver Usuário escolhe Projeto. 

    // Verifica se o usuario eh administrador deste projeto 
    if (is_admin($_SESSION['id_usuario_corrente'], $id_projeto)) {    
?>    

        <br> 
        <table ALIGN=CENTER> 
            <tr> 
                <th>Você é um administrador deste projeto:</th> 

<?php    

// Cenário - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
//            Pré-Condições: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Episódios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as opções de: 
//            -Verificar pedidos de alteração de cenário (ver Verificar pedidos de alteração 
//            de cenário); 
//            - Verificar pedidos de alteração de termos do léxico 
//            ( ver Verificar pedidos de alteração de termos do léxico); 
//            -Adicionar usuário (não existente) neste projeto (ver Adicionar Usuário); 
//            -Relacionar usuários já existentes com este projeto 
//            (ver Relacionar usuários com projetos); 
//            -Gerar xml deste projeto (ver Gerar relatórios XML); 

?>    
            </TR>
            
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="addUsuario();">Adicionar usuário (não cadastrado) neste projeto</a></td> 
            </TR> 
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="relUsuario();">Adicionar usuários já existentes neste projeto</a></td> 
            </TR>   
            
            <TR> 
                <td CLASS="Estilo">&nbsp;</td> 
            </TR> 
            
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="pedidoCenario();">Verificar pedidos de alteração de Cenários</a></td> 
            </TR> 
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="pedidoLexico();">Verificar pedidos de alteração de termos do Léxico</a></td> 
            </TR>
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="pedidoConceito();">Verificar pedidos de alteração de Conceitos</a></td> 
            </TR> 
            
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="pedidoRelacao();">Verificar pedidos de alteração de Relações</a></td> 
            </TR>
   
       
            <TR> 
                <td CLASS="Estilo">&nbsp;</td> 
            </TR> 
			<TR> 
                <td CLASS="Estilo"><a href="#" onClick="geraGrafo();" >Gerar grafo deste projeto</a></td>
		    </TR>       
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="geraXML();">Gerar XML deste projeto</a></td> 
            </TR> 
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="recuperaXML();">Recuperar XML deste projeto</a></td> 
            </TR> 
            
            <TR> 
                <td CLASS="Estilo">&nbsp;</td> 
            </TR> 
            
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="geraOntologia();">Gerar ontologia deste projeto</a></td> 
            </TR>            
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="geraDAML();">Gerar DAML da ontologia do projeto</a></td> 
            </TR> 
            <TR> 
                <td CLASS="Estilo"><a href="#" onClick="recuperaDAML();">Histórico em DAML da ontologia do projeto</a></td> 
            </TR>           
            <TR> 
                <td CLASS="Estilo"><a href="http://www.daml.org/validator/" target="new">*Validador de Ontologias na Web</a></td> 
            </TR>
            <TR> 
                <td CLASS="Estilo"><a href="http://www.daml.org/2001/03/dumpont/" target="new">*Visualizador de Ontologias na Web</a></td> 
            </TR>
             <TR> 
                <td CLASS="Estilo">&nbsp;</td> 
            </TR>
            <TR> 
                <td CLASS="Estilo"><font size="1">*Para usar Ontologias Geradas pelo C&L: </font></td>               
            </TR>
            <TR> 
                <td CLASS="Estilo">   <font size="1">Histórico em DAML da ontologia do projeto -> Botao Direito do Mouse -> Copiar Atalho</font></td>             
            </TR>
		</table>


<?php    
    }   else
	{
?>	
	<br>
	<table ALIGN=CENTER> 
            <tr> 
                <th>Você não é um administrador deste projeto:</th> 	
			</tr>	
			<tr> 
                <td CLASS="Estilo"><a href="#" onClick="geraGrafo();" >Gerar grafo deste projeto</a></td>
		    </tr>  
	</table>			
<?php
	}
} else {        // SCRIPT CHAMADO PELO INDEX.PHP 
?>    

        <p>Selecione um projeto acima, ou crie um novo projeto.</p> 

<?php    
}    
?>    
<i><a href="showSource.php?file=main.php">Veja o código fonte!</a></i> 
    </body> 

</html> 

