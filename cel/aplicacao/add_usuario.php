<?php
    session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php

include("funcoes_genericas.php");
include_once("bd.inc");

$primeira_vez = "true";

include("httprequest.inc");

if (isset($submit)) // Se chamado pelo botao de submit
{
    $primeira_vez = "false";
    // ** Cenario "Inclusao de Usuario Independente" **
    // O sistema checa se todos os campos estao preenchidos. Se algum nao estiver, o
    // sistema avisa pro usuario que todos os campos devem ser preenchidos.
    if ($nome == "" || $email == "" || $login == "" || $senha == "" || $senha_conf == "")
	{
        $p_style = "color: red; font-weight: bold";
        $p_text = "Por favor, preencha todos os campos.";
        recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&login=$login&senha=$senha&senha_conf=$senha_conf&novo=$novo");
    }
    else
    {
        // Testa se as senhas fornecidas pelo usuario sao iguais.
        if ($senha != $senha_conf)
        {
            $p_style = "color: red; font-weight: bold";
            $p_text = "Senhas diferentes. Favor preencher novamente as senhas.";
            recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&login=$login&novo=$novo");
        }
        else
        {

            // ** Cenario "Inclusao de Usuario Independente" **
            // Todos os campos estao preenchidos. O sistema deve agora verificar
            // se ja nao existe alguem cadastrado com o mesmo login informado pelo usuario.

			// Cen�rio - Incluir usu�rio independente 
			
			// Objetivo:  Permitir um usu�rio, que n�o esteja cadastrado como administrador, se cadastrar 
			//            com o perfil de administrador	
			// Contexto:  Sistema aberto Usu�rio deseja cadastrar-se ao sistema como administrador. 
			//            Usu�rio na tela de cadastro de usu�rio 
			//            Pr�-Condi��o: Usu�rio ter acessado ao sistema	
			// Atores:    Usu�rio, Sistema	
			// Recursos:  Interface, Banco de Dados	
			// Epis�dios: O sistema retorna para o usu�rio uma interface com campos para entrada de
			//            um Nome, email, login, uma senha e a confirma��o da senha.
			//            O usu�rio preenche os campos e clica em cadastrar 
			//            O sistema ent�o checa para ver se todos os campos est�o preenchidos.
			//              Caso algum campo deixar de ser preenchido, o sistema avisa que todos
			//               os campos devem ser preenchidos.
			//              Caso todos os campos estiverem preenchidos, o sistema checa no banco
			//               de dados para ver se esse login j� existe..
			//              Caso aquele login digitado j� exista, o sistema retorna a mesma p�gina
			//               para o usu�rio avisando que o usu�rio deve escolher outro login,.

            $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");
            $query = "SELECT id_usuario FROM usuario WHERE login = '$login'";
            $SendQuery = mysql_query($query) or die("Erro ao enviar a query");
            if (mysql_num_rows($SendQuery)) // Se ja existe algum usuario com este login
            {        
				// $p_style = "color: red; font-weight: bold";
				// $p_text = "Login ja existente no sistema. Favor escolher outro login.";
				// recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&senha=$senha&senha_conf=$senha_conf&novo=$novo");
	
	
				// Cen�rio - Adicionar Usuario
				
				// Objetivo:  Permitir ao Administrador criar novos usuarios.
				// Contexto:  O Administrador deseja adicionar novos usuarios (nao cadastrados)
				//            criando novos  usuarios ao projeto selecionado.
				//Pre-Condicoes: Login
				// Atores:    Administrador
				// Recursos:  Dados do usu�rio
				// Episodios: O Administrador clica no link Adicionar usuario (nao existente) neste projeto,
				//            entrando com as informa�oes do novo usuario: nome, email, login e senha.
				//            Caso o login ja exista, aparecer uma mensagem de erro na tela informando que
				//            este login ja existe.

                ?>
<script language="JavaScript">
                    alert ("Login j� existente no sistema. Favor escolher outro login.")
                </script>
<?php
                recarrega("?novo=$novo");
            }
            else // Cadastro passou por todos os testes -- ja pode ser incluido na BD
			{
				/* Substitui todas as ocorrencias de ">" e "<" por " " */
				$nome  = str_replace( ">" , " " , str_replace ( "<" , " " , $nome  ) ) ;
				$login = str_replace( ">" , " " , str_replace ( "<" , " " , $login ) ) ;
				$email = str_replace( ">" , " " , str_replace ( "<" , " " , $email ) ) ;
				
				// Criptografando a senha
				$senha = md5($senha);
                $query = "INSERT INTO usuario (nome, login, email, senha) VALUES ('$nome', '$login', '$email', '$senha')";
                mysql_query($query) or die("Erro ao cadastrar o usuario");
                recarrega("?cadastrado=&novo=$novo&login=$login");
            }
        }   // else
    }   // else
} else if (isset($cadastrado))
{
    // Cadastro concluido. Dependendo de onde o usuario veio,
    // devemos manda-lo para um lugar diferente.

    if ($novo == "true") // Veio da tela inicial de login
    {

        // ** Cenario "Inclusao de Usuario Independente" **
        // O usuario acabou de cadastrar-se no sistema, devemos
        // redireciona-lo para a parte de inclusao de projetos

        // Registra que o usuario esta logado com o login recem-cadastrado
		
		// Cen�rio - Incluir usu�rio independente 
		
		// Objetivo:  Permitir um usu�rio, que n�o esteja cadastrado como administrador, se cadastrar 
		//            com o perfil de administrador	
		// Contexto:  Sistema aberto Usu�rio deseja cadastrar-se ao sistema como administrador. 
		//            Usu�rio na tela de cadastro de usu�rio 
		//            Pr�-Condi��o: Usu�rio ter acessado ao sistema	
		// Atores:    Usu�rio, Sistema	
		// Recursos:  Interface, Banco de Dados	
		// Epis�dios:  Caso aquele login digitado n�o exista, o sistema cadastra esse usu�rio 
		//               como administrador no banco de dados,  possibilitando:
		//              - Redirecion�-lo  para a interface de CADASTRAR NOVO PROJETO; 

        $id_usuario_corrente = simple_query("id_usuario", "usuario", "login = '$login'");
        session_register("id_usuario_corrente");
?>
<script language="javascript1.3">

			// Redireciona o usuario para a parte de inclusao de projetos
			opener.location.replace('index.php');
			open('add_projeto.php', '', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');
			self.close();

		</script>
<?php
    }
    else
	{
	    // ** Cenario "Edicao de Usuario" **
	    // O administrador do projeto acabou de incluir o usuario.
	    // Devemos agora adicionar o usuario incluido no projeto
	    // do administrador.
	
	    // Conexao com a base de dados
	    $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");
	    // $login eh o login do usuario incluido, passado na URL
	    $id_usuario_incluido = simple_query("id_usuario", "usuario", "login = '$login'");
	    $query = "INSERT INTO participa (id_usuario, id_projeto)
	          VALUES ($id_usuario_incluido, " . $_SESSION['id_projeto_corrente'] . ")";
	    mysql_query($query) or die("Erro ao inserir na tabela participa");
	
	    $nome_usuario = simple_query("nome", "usuario", "id_usuario = $id_usuario_incluido");
	    $nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
?>
<script language="javascript1.3">
		
			document.writeln('<p style="color: blue; font-weight: bold; text-align: center">Usu�rio <b><?=$nome_usuario?></b> cadastrado e inclu�do no projeto <b><?=$nome_projeto?></b></p>');
			document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');
		
		</script>
<?php
    }
}
else // Script chamado normalmente
{    
    if (empty($p_style))
	{
        $p_style = "color: green; font-weight: bold";
        $p_text = "Favor preencher os dados abaixo:";
    }

    if ($primeira_vez)
    {
         $email ="";
	     $login ="";
	     $nome  ="";
	     $senha = "";
         $senha_conf = "";

    }
?>
<html>
<head>
<title>Cadastro de Usu�rio</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<script language="JavaScript">
	    	<!--
	        function verifyEmail(form)
	        {
	            email = form.email.value;
	            //verifica se o email contem um @
	            i = email.indexOf("@");
	            if (i == -1)
	        	{
	        	    alert('Aten��o: o E-mail digitado n�o � v�lido.');
	        	    return false;
	        	}
	        }
			
			function checkEmail(email)
			{
				if(email.value.length > 0)
				{
					if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
				    {
						return (true)
				    }
				    
				alert("Aten��o: o E-mail digitado n�o � v�lido.")
				email.focus();
				email.select();
				return (false)
				}
			}
	
	    	//-->

    	</SCRIPT>
<p style="<?=$p_style?>">
  <?=$p_text?>
</p>
<form action="?novo=<?=$novo?>" method="post">
  <table>
    <tr>
      <td>Nome:</td>
      <td colspan="3"><input name="nome" maxlength="255" size="48" type="text" value="<?=$nome?>"></td>
    </tr>
    <tr>
      <td>E-mail:</td>
      <td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?=$email?>" OnBlur="checkEmail(this)"></td>
    </tr>
    <tr>
      <td>Login:</td>
      <td><input name="login" maxlength="32" size="24" type="text" value="<?=$login?>"></td>
    </tr>
    <tr>
      <td>Senha:</td>
      <td><input name="senha" maxlength="32" size="16" type="password" value="<?=$senha?>"></td>
      <td>Senha (confirmacao):</td>
      <td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
    </tr>
    <tr>
      <?php

// Cenario - Adicionar Usuario

// Objetivo:  Permitir ao Administrador criar novos usuarios.
// Contexto:  O Administrador deseja adicionar novos usuarios (nao cadastrados) criando novos
//              usuarios ao projeto selecionado.
// Pre-Condicoes: Login
// Atores:    Administrador
// Recursos:  Dados do usuario
// Episodios: Clicando no botao Cadastrar para confirmar adicionando novo
//             usuario ao projeto selecionado.
//            O novo usuario criado recebera uma mensagem via email com seu login e senha.

?>
      <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return verifyEmail(this.form);" type="submit" value="Cadastrar"></td>
    </tr>
  </table>
</form>
<br>
<i><a href="showSource.php?file=add_usuario.php">Veja o codigo fonte!</a></i>
</body>
</html>
<?php
}
?>
