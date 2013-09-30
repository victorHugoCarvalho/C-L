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


// Checa se o usu�rio foi autenticado
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
else
{
	//Nothing to do.
}   


?>
<html>
        <head>
        <LINK rel="stylesheet" type="text/css" href="style.css">
        <script language="javascript1.3"> 

        // Funcoes que serao usadas quando o script for chamado atraves dele proprio ou da arvore 
        function reCarrega(URL)
        { 
            document.location.replace(URL); 
        } 

<?php    

// Cen�rio - Atualizar Cen�rio 

//Objetivo:    Permitir Inclus�o, Altera��o e Exclus�o de um Cen�rio por um usu�rio 
//Contexto:    Usu�rio deseja incluir um cen�rio ainda n�o cadastrado, alterar e/ou excluir 
//              um cen�rio previamente cadastrados. 
//Pr�-Condi��es: Login 
//Atores:    Usu�rio, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Epis�dios:    O usu�rio clica no menu superior na op��o: 
//                Se usu�rio clica em Alterar ent�o ALTERAR CEN�RIO 

?>    

        function altCenario(cenario)
        { 
            var url = 'alt_cenario.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_cenario=' + cenario; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=660,width=550,resizable,scrollbars,titlebar';
            open(url, where, window_spec); 
        } 

<?php    

// Cen�rio - Atualizar Cen�rio 

//Objetivo:    Permitir Inclus�o, Altera��o e Exclus�o o de um Cen�rio por um usu�rio 
//Contexto:    Usu�rio deseja incluir um cen�rio ainda n�o cadastrado, alterar e/ou excluir 
//              um cen�rio previamente cadastrados. 
//Pr�-Condi��es: Login 
//Atores:    Usu�rio, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Epis�dios:    O usu�rio clica no menu superior na op��o: 
//                Se usu�rio clica em Excluir ent�o EXCLUIR CEN�RIO 

?>    

        function rmvCenario(cenario)
        { 
            var url = 'rmv_cenario.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_cenario=' + cenario; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

//Cen�rio -  Atualizar L�xico 

//Objetivo:    Permitir Inclus�o, Altera��o e Exclus�o de um L�xico por um usu�rio 
//Contexto:    Usu�rio deseja incluir um lexico ainda n�o cadastrado, alterar e/ou 
//              excluir um cen�rio/l�xico previamente cadastrados. 
//Pr�-Condi��es: Login 
//Atores:    Usu�rio, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Epis�dios:    O usu�rio clica no menu superior na op��o: 
//                Se usu�rio clica em Alterar ent�o ALTERAR L�XICO 

?>    

        function altLexico(lexico)
        { 
            var url = 'alt_lexico.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_lexico=' + lexico; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
            open(url, where, window_spec); 
        } 

<?php    

//Cen�rio -  Atualizar L�xico 

//Objetivo:    Permitir Inclus�o, Altera��o e Exclus�o de um L�xico por um usu�rio 
//Contexto:    Usu�rio deseja incluir um lexico ainda n�o cadastrado, alterar e/ou 
//              excluir um cen�rio/l�xico previamente cadastrados. 
//Pr�-Condi��es: Login 
//Atores:    Usu�rio, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Epis�dios:    O usu�rio clica no menu superior na op��o: 
//                Se usu�rio clica em Excluir ent�o EXCLUIR L�XICO 

?>    

        function rmvLexico(lexico)
        { 
            var url = 'rmv_lexico.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_lexico=' + lexico; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

        // Funcoes que serao usadas quando o script 
        // for chamado atraves da heading.php 

<?php    

// Cen�rio - Atualizar Cen�rio 

//Objetivo:    Permitir Inclus�o, Altera��o e Exclus�o de um Cen�rio por um usu�rio 
//Contexto:    Usu�rio deseja incluir um cen�rio ainda n�o cadastrado, alterar e/ou excluir 
//              um cen�rio previamente cadastrados. 
//Pr�-Condi��es: Login 
//Atores:    Usu�rio, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Epis�dios:    O usu�rio clica no menu superior na op��o: 
//                Se usu�rio clica em Alterar ent�o ALTERAR CEN�RIO 

?>    

        function altConceito(conceito)
        { 
            var url = 'alt_conceito.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_conceito=' + conceito; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

// Cen�rio - Atualizar Conceito

//Objetivo:    Permitir Inclus�o, Altera��o e Exclus�o de um Cen�rio por um usu�rio 
//Contexto:    Usu�rio deseja incluir um Cen�rio ainda n�o cadastrado, alterar e/ou excluir 
//              um cen�rio previamente cadastrados. 
//Pr�-Condi��es: Login 
//Atores:    Usu�rio, Gerente do projeto 
//Recursos:    Sistema, menu superior, objeto a ser modificado 
//Epis�dios:    O usu�rio clica no menu superior na op��o: 
//                Se usu�rio clica em Excluir ent�o EXCLUIR CEN�RIO 

?>    

        function rmvConceito(conceito)
        { 
            var url = 'rmv_conceito.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_conceito=' + conceito; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 
        
        function rmvRelacao(relacao)
        {
            var url = 'rmv_relacao.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>' + '&id_relacao=' + relacao; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        }

<?php    


// Cen�rio - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Epis�dios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as op��es de: 
//            -Verificar pedidos de altera��o de cen�rio (ver Verificar pedidos de altera��o 
//            de cen�rios); 

?>    

        function pedidoCenario()
        { 
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

// Cen�rio - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Epis�dios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as op��es de: 
//            - Verificar pedidos de altera��o de termos do l�xico 
//            ( ver Verificar pedidos de altera��o de termos do l�xico); 

			?>    
        function pedidoLexico()
        { 

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

// Cen�rio - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Epis�dios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as op��es de: 
//            - Verificar pedidos de altera��o de termos do l�xico 
//            ( ver Verificar pedidos de altera��o de termos do l�xico); 

?>    

        function pedidoConceito()
        { 
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
        
        function pedidoRelacao()
        { 
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

// Cen�rio - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Epis�dios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as op��es de: 
//            -Adicionar usu�rios (n�o existente) neste projeto (ver Adicionar Usu�rios); 

?>    

        function addUsuario()
        { 
            var url = 'add_usuario.php'; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=320,width=490,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

// Cen�rio - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Epis�dios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as op��es de: 
//            -Relacionar usu�rios j� existentes com este projeto 
//            (ver Relacionar usu�rios com projetos); 

?>    

        function relUsuario()
        { 
            var url = 'rel_usuario.php'; 
            var where = '_blank'; 
            var window_spec = 'dependent,height=380,width=550,resizable,scrollbars,titlebar'; 
            open(url, where, window_spec); 
        } 

<?php    

// Cen�rio - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Epis�dios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as op��es de: 
//            -Gerar xml deste projeto (ver Gerar relat�rios XML); 

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
            }
            else    
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

// Objetivo: Recuperar hist�rico da ontologia em DAML 
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


if (isset($id) && isset($t))     // SCRIPT CHAMADO PELO PROPRIO MAIN.PHP (OU PELA ARVORE)
{  
    $vetorVazio = array();
    if ($t == "c")       
	{
		print "<h3>Informa&ccedil;&otilde;es sobre o cen&aacute;rio</h3>";   
    }
    else if ($t == "l") 
    {
    	print "<h3>Informa&ccedil;&otilde;es sobre o s&iacute;mbolo</h3>";   
    }
    else if ($t == "oc")
	{
		print "<h3>Informa&ccedil;&otilde;es sobre o conceito</h3>";    
    }
    else if ($t == "or")
	{
		print "<h3>Informa&ccedil;&otilde;es sobre a rela&ccedil;&atilde;o</h3>";   
    }
    else if ($t == "oa")
	{
		print "<h3>Informa&ccedil;&otilde;es sobre o axioma</h3>";   
    }
    else
    {
    	//Nothing to do.
    }    

?>
<table>
          
          <!--                     SEGUNDA PARTE                                     -->
          
          <?php    
    $c = bd_connect() or die("Erro ao conectar ao SGBD");    
?>
          
          <!-- CEN�RIO -->
          
          <?php   
    
	if ($t == "c")        // se for cenario
	{ 
        
		$q = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_projeto    
              FROM cenario    
              WHERE id_cenario = $id";    
        
		$qrr = mysql_query($q) or die("Erro ao enviar a query de sele&ccedil;&atilde;o !!". mysql_error());    
        $result = mysql_fetch_array($qrr);  
        
		$c_id_projeto = $result['id_projeto'];
		
		$vetorDeCenarios = carrega_vetor_cenario( $c_id_projeto, $id, true ); // carrega vetor de cenario
        quicksort( $vetorDeCenarios, 0, count($vetorDeCenarios)-1,'cenario' );
      
	    $vetorDeLexicos = carrega_vetor_lexicos( $c_id_projeto, 0, false ); // carrega vetor de l�xicos 
        quicksort( $vetorDeLexicos, 0, count($vetorDeLexicos)-1,'lexico' );
    		
?>
          <tr>
    <th>Titulo:</th>
    <td CLASS="Estilo"><?php echo nl2br(monta_links( $result['titulo'], $vetorDeLexicos, $vetorVazio)) ;?></td>
  </tr>
          <tr>
    <th>Objetivo:</th>
    <td CLASS="Estilo"><?php echo nl2br(monta_links( $result['objetivo'], $vetorDeLexicos, $vetorVazio )) ; ?></td>
  </tr>
          <tr>
    <th>Contexto:</th>
    <td CLASS="Estilo"><?php
    	    echo nl2br(monta_links( $result['contexto'], $vetorDeLexicos, $vetorDeCenarios ) ); ?></td>
  </tr>
          <tr>
    <th>Atores:</th>
    <td CLASS="Estilo"><?php echo nl2br(monta_links( $result['atores'], $vetorDeLexicos, $vetorVazio) ) ; ?></td>
  </tr>
          <tr>
    <th>Recursos:</th>
    <td CLASS="Estilo"><?php echo nl2br(monta_links( $result['recursos'], $vetorDeLexicos, $vetorVazio ) ) ; ?></td>
  </tr>
          <tr>
    <th>Exce&ccedil;&atilde;o:</th>
    <td CLASS="Estilo"><?php echo nl2br(monta_links( $result['excecao'], $vetorDeLexicos, $vetorVazio) ) ; ?></td>
  </tr>
          <tr>
    <th>Epis&oacute;dios:</th>
    <td CLASS="Estilo"><?php 
	  		echo nl2br(monta_links( $result['episodios'], $vetorDeLexicos, $vetorDeCenarios ) ); ?></td>
  </tr>
        </TABLE>
<BR>
<TABLE>
          <tr>
    <td CLASS="Estilo" height="40" valign=MIDDLE><a href="#" onClick="altCenario(<?=$result['id_cenario']?>);">Alterar Cen&aacute;rio</a>
              </th>
            <td CLASS="Estilo"  valign=MIDDLE><a href="#" onClick="rmvCenario(<?=$result['id_cenario']?>);">Remover Cen&aacute;rio</a>
              </th>
          </tr>
          
          <!-- L�XICO -->
          
          <?php    
    }
    else if ($t == "l")
	{          
        $q = "SELECT id_lexico, nome, nocao, impacto, tipo, id_projeto FROM lexico WHERE id_lexico = $id";    
      
		$qrr = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());    
        $result = mysql_fetch_array($qrr);
        
        $l_id_projeto = $result['id_projeto'];
        
        $vetorDeLexicos = carrega_vetor_lexicos( $l_id_projeto, $id, true );  
		
        quicksort( $vetorDeLexicos, 0, count( $vetorDeLexicos )-1,'lexico' );
 
?>
          <tr>
    <th>Nome:</th>
    <td CLASS="Estilo"><?php echo $result['nome']; ?></td>
  </tr>
          <tr>
    <th>No&ccedil;&atilde;o:</th>
    <td CLASS="Estilo"><?php echo nl2br( monta_links( $result['nocao'], $vetorDeLexicos, $vetorVazio ) ); ?></td>
  </tr>
          <tr>
    <th>Classifica&ccedil;&atilde;o:</th>
    <td CLASS="Estilo"><?=nl2br( $result['tipo'] ) ?></td>
  </tr>
          <tr>
    <th>Impacto(s):</th>
    <td CLASS="Estilo"><?php echo nl2br( monta_links( $result['impacto'], $vetorDeLexicos, $vetorVazio ) ); ?></td>
  </tr>
          <tr>
    <th>Sin&ocirc;nimo(s):</th>
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
    <td CLASS="Estilo"><?php                    
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

			 ?></td>
  </tr>
        </TABLE>
<BR>
<TABLE>
          <tr>
    <td CLASS="Estilo" height="40" valign="middle"><a href="#" onClick="altLexico(<?=$result['id_lexico']?>);">Alterar S&iacute;mbolo</a>
              </th>
            <td CLASS="Estilo" valign="middle"><a href="#" onClick="rmvLexico(<?=$result['id_lexico']?>);">Remover S&iacute;mbolo</a>
              </th>
          </tr>
          
          <!-- ONTOLOGIA - CONCEITO -->
          
          <?php    
    }
    else if ($t == "oc") // se for cenario
	{         
        
		$q = "SELECT id_conceito, nome, descricao   
              FROM   conceito   
              WHERE  id_conceito = $id";    
        
		$qrr = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());    
        $result = mysql_fetch_array($qrr);    
?>
          <tr>
    <th>Nome:</th>
    <td CLASS="Estilo"><?=$result['nome']?></td>
  </tr>
          <tr>
    <th>Descri&ccedil;&atilde;o:</th>
    <td CLASS="Estilo"><?=nl2br($result['descricao'])?></td>
  </tr>
        </TABLE>
<BR>
<TABLE>
          <tr>
    <td CLASS="Estilo" height="40" valign=MIDDLE></th>
            <td CLASS="Estilo"  valign=MIDDLE><a href="#" onClick="rmvConceito(<?=$result['id_conceito']?>);">Remover Conceito</a>
              </th>
          </tr>
          
          <!-- ONTOLOGIA - RELA��ES -->
          
          <?php    
    }
    elseif ($t == "or") // se for cenario
    { 
        $q = "SELECT id_relacao, nome   
              FROM relacao   
              WHERE id_relacao = $id";    
        $qrr = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());    
        $result = mysql_fetch_array($qrr);    
?>
          <tr>
    <th>Nome:</th>
    <td CLASS="Estilo"><?=$result['nome']?></td>
  </tr>
        </TABLE>
<BR>
<TABLE>
          <tr>
    <td CLASS="Estilo" height="40" valign=MIDDLE></th>
            <td CLASS="Estilo"  valign=MIDDLE><a href="#" onClick="rmvRelacao(<?=$result['id_relacao']?>);">Remover Rela&ccedil;&atilde;o</a>
              </th>
          </tr>
          <?php    
    }
    else
    {
    	//Nothing to do.
    }    
?>
        </table>
<br>

<!--                     TERCEIRA PARTE                                     -->

<?php    
    if ($t == "c")
	{ 
		print "<h3>Cen&aacute;rios que referenciam este cen&aacute;rio</h3>";   
    }
    else if ($t == "l")
	{
		print "<h3>Cen&aacute;rios e termos do l&eacute;xico que referenciam este termo</h3>";   
    }
    else if ($t == "oc")
	{ 
		print "<h3>Rela&ccedil;&otilde;es do conceito</h3>";   
    }
    else if ($t == "or")
	{
		print "<h3>Conceitos referentes &agrave; rela&ccedil;&atilde;o</h3>";   
    }
    else if ($t == "oa")
	{
		print "<h3>Axioma</h3>";
    }
    else
    {
    	//Nothing to do.
    }   
?>

<!--                     QUARTA PARTE                                     -->

<?php   

    frame_inferior($c, $t, $id);    

}
else if (isset($id_projeto))         // SCRIPT CHAMADO PELO HEADING.PHP
{ 

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
    <th>Data de cria&ccedil;&atilde;o:</th>
    <?php    
                    $data = simple_query("data_criacao", "projeto", "id_projeto = $id_projeto");    
                ?>
    <td CLASS="Estilo"><?=formataData($data)?></td>
  </tr>
          <tr>
    <th>Descri&ccedil;&atilde;o:</th>
    <td CLASS="Estilo"><?=nl2br(simple_query("descricao", "projeto", "id_projeto = $id_projeto"))?></td>
  </tr>
        </table>
<?php    

// Cen�rio - Escolher Projeto 

// Objetivo:  Permitir ao Administrador/Usu�rio escolher um projeto. 
// Contexto:  O Administrador/Usu�rio deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser Administrador 
// Atores:    Administrador, Usu�rio
// Recursos:  Usu�rios cadastrados 
// Epis�dios: Caso o Usuario selecione da lista de projetos um projeto da qual ele seja 
//            administrador, ver Administrador escolhe Projeto. 
//            Caso contr�rio, ver Usu�rio escolhe Projeto. 

    // Verifica se o usuario eh administrador deste projeto 
    if (is_admin($_SESSION['id_usuario_corrente'], $id_projeto))
	{    
?>
<br>
<table ALIGN=CENTER>
          <tr>
    <th>Voc&ecirc; &eacute; um administrador deste projeto:</th>
    <?php    

// Cen�rio - Administrador escolhe Projeto 

// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Epis�dios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as op��es de: 
//            -Verificar pedidos de altera��o de cen�rio (ver Verificar pedidos de altera��o 
//            de cen�rio); 
//            - Verificar pedidos de altera��o de termos do l�xico 
//            ( ver Verificar pedidos de altera��o de termos do l�xico); 
//            -Adicionar usu�rio (n�o existente) neste projeto (ver Adicionar Usu�rio); 
//            -Relacionar usu�rios j� existentes com este projeto 
//            (ver Relacionar usu�rios com projetos); 
//            -Gerar xml deste projeto (ver Gerar relat�rios XML); 

?>
  </TR>
          <TR>
    <td CLASS="Estilo"><a href="#" onClick="addUsuario();">Adicionar usu&aacute;rio (n&atilde;o cadastrado) neste projeto</a></td>
  </TR>
          <TR>
    <td CLASS="Estilo"><a href="#" onClick="relUsuario();">Adicionar usu&aacute;rios j&aacute; existentes neste projeto</a></td>
  </TR>
          <TR>
    <td CLASS="Estilo">&nbsp;</td>
  </TR>
          <TR>
    <td CLASS="Estilo"><a href="#" onClick="pedidoCenario();">Verificar pedidos de altera&ccedil;&atilde;o de Cen&aacute;rios</a></td>
  </TR>
          <TR>
    <td CLASS="Estilo"><a href="#" onClick="pedidoLexico();">Verificar pedidos de altera&ccedil;&atilde;o de termos do L&eacute;xico</a></td>
  </TR>
          <TR>
    <td CLASS="Estilo"><a href="#" onClick="pedidoConceito();">Verificar pedidos de altera&ccedil;&atilde;o de Conceitos</a></td>
  </TR>
          <TR>
    <td CLASS="Estilo"><a href="#" onClick="pedidoRelacao();">Verificar pedidos de altera&ccedil;&atilde;o de Rela&ccedil;&otilde;es</a></td>
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
    <td CLASS="Estilo"><a href="#" onClick="recuperaDAML();">Hist&oacute;rico em DAML da ontologia do projeto</a></td>
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
    <td CLASS="Estilo"><font size="1">Hist&oacute;rico em DAML da ontologia do projeto -> Botao Direito do Mouse -> Copiar Atalho</font></td>
  </TR>
        </table>
<?php    
    }  
    else
	{
?>
<br>
<table ALIGN=CENTER>
          <tr>
    <th>Voc&ecirc; n&atilde;o &eacute; um administrador deste projeto:</th>
  </tr>
          <tr>
    <td CLASS="Estilo"><a href="#" onClick="geraGrafo();" >Gerar grafo deste projeto</a></td>
  </tr>
        </table>
<?php
	}
}
else        // SCRIPT CHAMADO PELO INDEX.PHP
{ 
?>
<p>Selecione um projeto acima, ou crie um novo projeto.</p>
<?php    
}    
?>
<i><a href="showSource.php?file=main.php">Veja o c&oacute;digo fonte!</a></i>
</body>
</html>
