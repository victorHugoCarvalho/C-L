<?php
include("bd.inc");
include("httprequest.inc");

// Cen�rio - Lembrar senha 

//Objetivo:	 Permitir o usu�rio cadastrado, que esqueceu sua senha,  receber  a mesma por email	
//Contexto:	 Sistema est� aberto, Usu�rio esqueceu sua senha Usu�rio na tela de lembran�a de 
//           senha. 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:	 Usu�rio, Sistema	
//Recursos:	 Banco de Dados	
//Epis�dios: O sistema verifica se o login informado � cadastrado no banco de dados.     
//           Se o login informado for cadastrado, sistema consulta no banco de dados qual 
//           o email e senha do login informado.           
 
$r = bd_connect() or die("Erro ao conectar ao SGBD");

$q = "SELECT * FROM usuario WHERE login='$login'";

$qrr = mysql_query($q) or die("Erro ao executar a query");


?>
<html>
<head>
<title>Enviar senha</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF">
<?php
if ( !mysql_num_rows($qrr) )
{

?>
<p style="color: red; font-weight: bold; text-align: center">Login inexistente!</p>
<center>
  <a href="JavaScript:window.history.go(-1)">Voltar</a>
</center>
<?php
}
else
{
   $row = mysql_fetch_row($qrr);
   $nome  = $row[1];
   $mail  = $row[2];
   $login = $row[3];
   $senha = $row[4];
   
// Cen�rio - Lembrar senha 

//Objetivo:	 Permitir o usu�rio cadastrado, que esqueceu sua senha,  receber  a mesma por email	
//Contexto:	 Sistema est� aberto, Usu�rio esqueceu sua senha Usu�rio na tela de lembran�a de 
//           senha. 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:	 Usu�rio, Sistema	
//Recursos:	 Banco de Dados	
//Epis�dios: Sistema envia a senha para o email cadastrado correspondente ao login que 
//           foi informado pelo usu�rio.     
//           Caso n�o exista nenhum login cadastrado igual ao informado pelo usu�rio, 
//           sistema exibe mensagem de erro na tela dizendo que login � inexistente, e 
//           exibe um bot�o voltar, que redireciona o usu�rio para a tela de login novamente.

   //$Vemail = ini_set("SMTP","mail.gmail.com");  

   //require("class.phpmailer.php");
   // Seta o SMTP sem alterar o config
   //ini_set("SMTP","mail.hotpop.com");
   
   //Funcao que gera uma senha randomica de 6 caracteres

	function gerarandonstring($n)
	{	
		$str = "ABCDEFGHIJKLMNOPQRSTUVXYWZabcdefghijklmnopqrstuvxywz0123456789";
		$cod = "";
		for($a = 0;$a < $n;$a++)
		{		
			$rand = rand(0,61);
			$cod .= substr($str,$rand,1);
		}	
		return $cod;
	}// Chamando a fun��o: gerarandonstring([quantidadedecaracteres])echo gerarandonstring(20);
  
	// Gera uma nova senha rand�mica	
   $nova_senha = gerarandonstring(6);
   //Criptografa senha
   $nova_senha_cript = md5($nova_senha);
   
   // Substitui senha antiga pela nova senha no banco de dados
   
   $qUp = "update usuario set senha = '$nova_senha_cript' where login = '$login'";
   $qrrUp = mysql_query($qUp) or die("Erro ao executar a query de update na tabela usuario");
   
   $corpo_email = "Caro $nome,\n Como solicitado, estamos enviando sua nova senha para acesso ao sistema C&L.\n\n login: $login \n senha: $nova_senha \n\n Para evitar futuros transtornos altere sua senha o mais breve poss&iacute;vel. \n Obrigado! \n Equipe de Suporte do C&L.";
   $headers = "";
   if(mail("$mail", "Nova senha do C&L" , "$corpo_email" , $headers))
   { 	
   ?>
<p style="color: red; font-weight: bold; text-align: center">Uma nova senha foi criada e enviada para seu e-mail cadastrado.</p>
<center>
  <a href="JavaScript:window.history.go(-2)">Voltar</a>
</center>
<?php
   }
   else
   {
	?>
<p style="color: red; font-weight: bold; text-align: center">Ocorreu um erro durante o envio do e-mail!</p>
<center>
  <a href="JavaScript:window.history.go(-2)">Voltar</a>
</center>
<?php
   
   }

}
?>
</body>
</html>
