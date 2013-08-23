<?php
    session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php

include("funcoes_genericas.php");
include_once("bd.inc");

$primeira_vez = "true";

include("httprequest.inc");

if (isset($submit)) {   // Se chamado pelo botao de submit

     $primeira_vez = "false";
    // ** Cenario "Inclusao de Usuario Independente" **
    // O sistema checa se todos os campos estao preenchidos. Se algum nao estiver, o
    // sistema avisa pro usuario que todos os campos devem ser preenchidos.
    if ($nome == "" || $email == "" || $login == "" || $senha == "" || $senha_conf == "") {
        $p_style = "color: red; font-weight: bold";
        $p_text = "Por favor, preencha todos os campos.";
        recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&login=$login&senha=$senha&senha_conf=$senha_conf&novo=$novo");
    } else {

        // Testa se as senhas fornecidas pelo usuario sao iguais.
        if ($senha != $senha_conf) {
            $p_style = "color: red; font-weight: bold";
            $p_text = "Senhas diferentes. Favor preencher novamente as senhas.";
            recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&login=$login&novo=$novo");
        } else {

            // ** Cenario "Inclusao de Usuario Independente" **
            // Todos os campos estao preenchidos. O sistema deve agora verificar
            // se ja nao existe alguem cadastrado com o mesmo login informado pelo usuario.

// Cenário - Incluir usuário independente 

// Objetivo:  Permitir um usuário, que não esteja cadastrado como administrador, se cadastrar 
//            com o perfil de administrador	
// Contexto:  Sistema aberto Usuário deseja cadastrar-se ao sistema como administrador. 
//            Usuário na tela de cadastro de usuário 
//            Pré-Condição: Usuário ter acessado ao sistema	
// Atores:    Usuário, Sistema	
// Recursos:  Interface, Banco de Dados	
// Episódios: O sistema retorna para o usuário uma interface com campos para entrada de
//            um Nome, email, login, uma senha e a confirmação da senha.
//            O usuário preenche os campos e clica em cadastrar 
//            O sistema então checa para ver se todos os campos estão preenchidos.
//              Caso algum campo deixar de ser preenchido, o sistema avisa que todos
//               os campos devem ser preenchidos.
//              Caso todos os campos estiverem preenchidos, o sistema checa no banco
//               de dados para ver se esse login já existe..
//              Caso aquele login digitado já exista, o sistema retorna a mesma página
//               para o usuário avisando que o usuário deve escolher outro login,.

            $r = bd_connect() or die("Erro ao conectar ao SGBD");
            $q = "SELECT id_usuario FROM usuario WHERE login = '$login'";
            $qrr = mysql_query($q) or die("Erro ao enviar a query");
            if (mysql_num_rows($qrr)) {        // Se ja existe algum usuario com este login
//                $p_style = "color: red; font-weight: bold";
//                $p_text = "Login já existente no sistema. Favor escolher outro login.";
//                recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&senha=$senha&senha_conf=$senha_conf&novo=$novo");


// Cenário - Adicionar Usuário

// Objetivo:  Permitir ao Administrador criar novos usuários.
// Contexto:  O Administrador deseja adicionar novos usuários (não cadastrados)
//            criando novos  usuários ao projeto selecionado.
//            Pré-Condições: Login
// Atores:    Administrador
// Recursos:  Dados do usuário
// Episódios: O Administrador clica no link “Adicionar usuário (não existente) neste projeto”,
//            entrando com as informações do novo usuário: nome, email, login e senha.
//            Caso o login já exista, aparecerá uma mensagem de erro na tela informando que
//            este login já existe.

                ?>
                <script language="JavaScript">
                    alert ("Login já existente no sistema. Favor escolher outro login.")
                </script>

                <?php
                  recarrega("?novo=$novo");
            } else {    // Cadastro passou por todos os testes -- ja pode ser incluido na BD
				/* Substitui todas as ocorrencias de ">" e "<" por " " */
				$nome  = str_replace( ">" , " " , str_replace ( "<" , " " , $nome  ) ) ;
				$login = str_replace( ">" , " " , str_replace ( "<" , " " , $login ) ) ;
				$email = str_replace( ">" , " " , str_replace ( "<" , " " , $email ) ) ;
				
				// Criptografando a senha
				$senha = md5($senha);
                $q = "INSERT INTO usuario (nome, login, email, senha) VALUES ('$nome', '$login', '$email', '$senha')";
                mysql_query($q) or die("Erro ao cadastrar o usuario");
                recarrega("?cadastrado=&novo=$novo&login=$login");
            }
        }   // else
    }   // else
} elseif (isset($cadastrado)) {

    // Cadastro concluido. Dependendo de onde o usuario veio,
    // devemos manda-lo para um lugar diferente.

    if ($novo == "true") {      // Veio da tela inicial de login

        // ** Cenario "Inclusao de Usuario Independente" **
        // O usuario acabou de cadastrar-se no sistema, devemos
        // redireciona-lo para a parte de inclusao de projetos

        // Registra que o usuario esta logado com o login recem-cadastrado

// Cenário - Incluir usuário independente 

// Objetivo:  Permitir um usuário, que não esteja cadastrado como administrador, se cadastrar 
//            com o perfil de administrador	
// Contexto:  Sistema aberto Usuário deseja cadastrar-se ao sistema como administrador. 
//            Usuário na tela de cadastro de usuário 
//            Pré-Condição: Usuário ter acessado ao sistema	
// Atores:    Usuário, Sistema	
// Recursos:  Interface, Banco de Dados	
// Episódios:  Caso aquele login digitado não exista, o sistema cadastra esse usuário 
//               como administrador no banco de dados,  possibilitando:
//              - Redirecioná-lo  para a interface de CADASTRAR NOVO PROJETO; 

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
    } else {

    // ** Cenario "Edicao de Usuario" **
    // O administrador do projeto acabou de incluir o usuario.
    // Devemos agora adicionar o usuario incluido no projeto
    // do administrador.

    // Conexao com a base de dados
    $r = bd_connect() or die("Erro ao conectar ao SGBD");
    // $login eh o login do usuario incluido, passado na URL
    $id_usuario_incluido = simple_query("id_usuario", "usuario", "login = '$login'");
    $q = "INSERT INTO participa (id_usuario, id_projeto)
          VALUES ($id_usuario_incluido, " . $_SESSION['id_projeto_corrente'] . ")";
    mysql_query($q) or die("Erro ao inserir na tabela participa");

    $nome_usuario = simple_query("nome", "usuario", "id_usuario = $id_usuario_incluido");
    $nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
?>

<script language="javascript1.3">

document.writeln('<p style="color: blue; font-weight: bold; text-align: center">Usuário <b><?=$nome_usuario?></b> cadastrado e incluído no projeto <b><?=$nome_projeto?></b></p>');
document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');

</script>

<?php
    }
} else {    // Script chamado normalmente
    if (empty($p_style)) {
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
        <title>Cadastro de Usuário</title>
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
        	    alert('Atenção: o E-mail digitado não é válido.');
        	    return false;
        	}
        }

function checkEmail(email) {
  if(email.value.length > 0)
  {
     if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
     {
        return (true)
     }
   alert("Atenção: o E-mail digitado não é válido.")
   email.focus();
   email.select();
   return (false)
  }
}

    //-->



    </SCRIPT>

        <p style="<?=$p_style?>"><?=$p_text?></p>
        <form action="?novo=<?=$novo?>" method="post">
        <table>
            <tr>
                <td>Nome:</td><td colspan="3"><input name="nome" maxlength="255" size="48" type="text" value="<?=$nome?>"></td>
            </tr>
            <tr>
                <td>E-mail:</td><td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?=$email?>" OnBlur="checkEmail(this)"></td>
            </tr>
            <tr>
                <td>Login:</td><td><input name="login" maxlength="32" size="24" type="text" value="<?=$login?>"></td>
            </tr>
            <tr>
                <td>Senha:</td><td><input name="senha" maxlength="32" size="16" type="password" value="<?=$senha?>"></td>
                <td>Senha (confirmação):</td><td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
            </tr>
            <tr>

<?php

// Cenário - Adicionar Usuário

// Objetivo:  Permitir ao Administrador criar novos usuários.
// Contexto:  O Administrador deseja adicionar novos usuários (não cadastrados) criando novos
//              usuários ao projeto selecionado.
//            Pré-Condições: Login
// Atores:    Administrador
// Recursos:  Dados do usuário
// Episódios: Clicando no botão Cadastrar para confirmar a adição do novo
//             usuário ao projeto selecionado.
//            O novo usuário criado receberá uma mensagem via email com seu login e senha.

?>

                <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return verifyEmail(this.form);" type="submit" value="Cadastrar"></td>
            </tr>
        </table>
        </form>
        <br><i><a href="showSource.php?file=add_usuario.php">Veja o código fonte!</a></i>
    </body>
</html>

<?php
}
?>
