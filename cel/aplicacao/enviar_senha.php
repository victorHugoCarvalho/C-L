<?php
include("bd.inc");
include("httprequest.inc");

// Scenery - Remember password

// Purpose: Allow registered user, you forgot your password, you receive the same email
// Context: System is open, User User Forgot password reminder on screen password.
// Precondition: User has accessed the system
// Actors: User, System
// Resource: Database
// Episodes: The system checks if the login entered is registered in the database.
// If the login entered is registered, the system queries the database 
// which the login email and password entered.         
 
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");
$query = "SELECT * FROM usuario WHERE login='$login'";
$ExecuteQuery = mysql_query($query) or die("Erro ao executar a query");


?>
<html>
<head>
<title>Enviar senha</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF">
<?php
if (!mysql_num_rows($ExecuteQuery))
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
    $row = mysql_fetch_row($ExecuteQuery);
    $nome  = $row[1];
    $mail  = $row[2];
    $login = $row[3];
    $senha = $row[4];
   
    // Scenery - Remember password

    // Purpose: Allow registered user, you forgot your password, you receive the same email
    // Context: System is open, User User Forgot password reminder on screen
    // Password.
    // Precondition: User has accessed the system
    // Actors: User, System
    // Resource: Database
    // Episodes: System sends the password to the email address corresponding to the login that
    // Was entered by the user.
    // If no registered login equal to the user entered,
    // System displays error message on the screen saying that login does not exist, and
    // Displays a back button, which redirects the user to the login screen again.
   
   //Funcao que gera uma senha randomica de 6 caracteres

    function gerarandonstring($n)
    {
            assert($n > 0, "the number must be more than 0");

            $str = "ABCDEFGHIJKLMNOPQRSTUVXYWZabcdefghijklmnopqrstuvxywz0123456789";
            $cod = "";
            for($a = 0; $a < $n; $a++)
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

    $queryUpdate = "update usuario set senha = '$nova_senha_cript' where login = '$login'";
    $queryResultUpdate = mysql_query($queryUpdate) or die("Erro ao executar a query de update na tabela usuario");

    $corpo_email = "Caro $nome,\n Como solicitado, estamos enviando sua nova senha para acesso ao sistema C&L.\n\n login: $login \n senha: $nova_senha \n\n Para evitar futuros transtornos altere sua senha o mais breve poss&iacute;vel. \n Obrigado! \n Equipe de Suporte do C&L.";
    $headers = "";

    if (mail("$mail", "Nova senha do C&L" , "$corpo_email" , $headers))
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
