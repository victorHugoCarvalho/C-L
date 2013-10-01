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

// Scenery - Remember password

// Purpose: Allow registered user, you forgot your password, you receive the same emai
// Context: System is open, User forgot password screen in the User password reminder.
// Precondition: User has accessed the system
// Actors: User, System
// Resource: Database
// Episodes: The user accesses the login screen of the system.
// The user clicks the link? Forgot Password?
// The system displays a message on the screen asking the user to enter the login text box.
// The user enters his login and click on the Submit button.
             	
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
