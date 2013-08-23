<?php

/**
@Titulo: Acessar o sistema

@Objetivo: Permitir que o usuário acesse a Aplicação de Edição de Léxicos e de Edição de Cenários, cadastre-se no sistema ou requisite sua senha no caso de tê-la esquecido.

@Contexto: A página da aplicação é acessada. Na página de abertura ../cel/aplicacao/login.php o usuário insere login ou senha incorretos - $wrong=true.

@Atores: usuário, aplicação

@Recursos: URL de acesso ao sistema,  login, senha, bd.inc, httprequest.inc, $wrong, $url, showSource.php?file=login.php, esqueciSenha.php, add_usuario.php?novo=true 
**/

/** @Episodio 1: Iniciar sessão **/
session_start();

include("bd.inc");

$url = '';
$submit = '';
$login = '';
$senha = '';
$wrong = "false";

include("httprequest.inc");

/** @Episodio 2: Conectar o SGBD **/
/** @Restrição: a função bd_connect definida em bd.inc é utilizada **/
/** @Exceção: Erro ao conectar banco de dados **/

$r = bd_connect() or die("Erro ao conectar ao SGBD");

/** @Episodio 9: Se o formulário tiver sido submetido então verificar se o login e senha estão corretos. **/
if ( $submit == 'Entrar') 
{        
	$senha_cript = md5($senha);
	$q = "SELECT id_usuario FROM usuario WHERE login='$login' AND senha='$senha_cript'";
    $qrr = mysql_query($q) or die("Erro ao executar a query");
  
	/** @Episodio 10: Se o login e/ou senha estiverem incorretos então retornar a página de login com wrong=true na URL. **/
	if ( !mysql_num_rows($qrr) ) {        

?>
		<script language="javascript1.3">
			document.location.replace('login.php?wrong=true&url=<?=$url?>');
		</script>

<?php

		$wrong = $_get["wrong"];
    } 

	/** @Episodio 11: Se o login e senha estiverem corretos então registrar sessão para o usuário, fechar login.php e abrir aplicação . **/
	else {

        $row = mysql_fetch_row($qrr);
        $id_usuario_corrente = $row[0];

        session_register("id_usuario_corrente");
?>
		<script language="javascript1.3">
			opener.document.location.replace('<?=$url?>');
			self.close();
		</script>

<?php
    }
} 

/** @Episodio 3: Mostrar o formulário de login para usuário. **/
else {    
?>

<html>
    <head>
        <title>Entre com seu Login e Senha</title>
    </head>
    <body>

<?php

	/** @Episodio 4: Se wrong = true então mostrar a mensagem Login ou Senha incorreto . **/
	if ($wrong=="true") {
		?>

		<p style="color: red; font-weight: bold; text-align: center">
		<img src="Images/Logo_CEL.jpg" width="180" height="180"><br/><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;Login ou Senha Incorreto</p>

		<?php
	} 
	/** @Episodio 5: Se wrong != true então mostrar a mensagem Entre com seu login e senha. **/
	else {
		?>

		<p style="color: green; font-weight: bold; text-align: center">
		<img src="Images/Logo_CEL.jpg" width="100" height="100"><br/><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;Entre com seu Login e Senha:</p>

		<?php
	}
?>

	<form action="?url=<?=$url?>" method="post">
    <div align="center">
    <table cellpadding="5">
      <tr><td>Login:</td><td><input maxlength="32" name="login" size="24" type="text"></td></tr>
      <tr><td>Senha:</td><td><input maxlength="32" name="senha" size="24" type="password"></td></tr>
      <tr><td height="10"></td></tr>
      <tr><td align="center" colspan="2"><input name="submit" type="submit" value="Entrar"></td></tr>
    </table>

<?php 		/** @Episodio 6: [CADASTRAR NOVO USUÁRIO] **/ ?>
            <p><a href="add_usuario.php?novo=true">Cadastrar-se</a>&nbsp;&nbsp;

<?php 	    /** @Episodio 7: [LEMBRAR SENHA] **/ ?>
            <a href="esqueciSenha.php">Esqueci senha</a></p>
        </div>
        </form>
    </body>

<?php		/** @Episodio 8: [MOSTRAR O CÓDIGO FONTE] **/ ?>

	<i><a href="showSource.php?file=login.php">Veja o código fonte!</a></i>    
</html>

<?php
}
?>