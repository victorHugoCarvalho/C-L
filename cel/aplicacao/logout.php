<?php
include("bd.inc");
include_once("CELConfig/CELConfig.inc");

session_start();


// Cenário - Realizar logout

// Objetivo:  Permitir ao usuário realizar o logout, mantendo a integridade do que foi 
//            realizado,  e retorna a tela de login	
// Contexto:  Sistema aberto. Usuário ter acessado ao sistema. 
//            Usuário deseja sair da aplicação e manter a integridade do que foi 
//            realizado 
//            Pré-Condição: Usuário ter acessado ao sistema	
// Atores:	  Usuário, Sistema.	
// Recursos:  Interface	
// Episódios: O sistema fecha a sessão do usuário, mantendo a integridade do que foi realizado 
//            O sistema retorna a interface de login, possibilitando o usuário se logar 
//            novamente 	

	session_destroy();
	session_unset();
	$ipValor = CELConfig_ReadVar("HTTPD_ip") ;
?>

<html>
<script language="javascript1.3">


document.writeln('<p style="color: blue; font-weight: bold; text-align: center">A aplicação teminou escolha uma das opções abaixo:</p>');
document.writeln('<p align="center"><a href="javascript:logoff();">Entrar novamente</a></p>');
document.writeln('<p align="center"><a href="http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . "../" ); ?>">Página inicial</a></p>');
document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');

function logoff()
{
   location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") ); ?>index.php";
}


//window.close();
//location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") ); ?>index.php";
</script>
</html>

