<html>
<head>
<title>Esqueci minha senha</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="JavaScript">
<!--
function TestarBranco(form)
{ 
login = form.login.value

  if ((login == ""))
    { 
      alert ("Por favor, digite o seu Login.") 
      form.login.focus() 
      return false;
    }
  else
  {
      //Nothing to do.

}

//-->
</SCRIPT>
<p style="color: red; font-weight: bold; text-align: center"><img src="Images/Logo_CEL.jpg" width="180" height="100"><br/>
  <br/>
</p>

<body bgcolor="#FFFFFF">
<form action="enviar_senha.php" method="post">
  <div align="center">
    <?php

// Cen�rio - Lembrar senha 

//Objetivo:   Permitir o usu�rio cadastrado, que esqueceu sua senha,  receber  a mesma por email	
//Contexto:   Sistema est� aberto, Usu�rio esqueceu sua senha Usu�rio na tela de lembran�a de 
//             senha. 
//            Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:     Usu�rio, Sistema	
//Recursos:   Banco de Dados	
//Epis�dios:  O usu�rio acessa a tela de login do sistema. 
//            O usu�rio clica no link �Esqueci senha� 
//            O sistema apresenta uma mensagem na tela, pedindo ao usu�rio que digite o seu 
//            login na caixa de texto. 
//            O usu�rio digita o seu login e clica no bot�o Enviar. 
             	
?>
    <p style="color: green; font-weight: bold; text-align: center">Entre com seu Login:</p>
    <table cellpadding="5">
      <tr>
        <td>Login:</td>
        <td><input maxlength="12" name="login" size="24" type="text"></td>
      </tr>
      <tr>
        <td height="10"></td>
      </tr>
      <tr>
        <td align="center" colspan="2"><input name="submit"  onClick="return TestarBranco(this.form);" type="submit" value="Enviar"></td>
      </tr>
    </table>
  </div>
  <br>
  <br>
  <center>
    <a href="JavaScript:window.history.go(-1)">Voltar</a>
  </center>
</form>
<i><a href="showSource.php?file=esqueciSenha.php">Veja o c&oacute;digo fonte!</a></i>
</body>
</html>
