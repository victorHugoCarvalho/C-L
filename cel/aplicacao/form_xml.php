<?php

session_start();

include("funcoes_genericas.php");

chkUser("index.php");        // Checa se o usuario foi autenticado

?>
<html>

<body>
<head>
<title>Gerar XML</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<form action="gerador_xml.php" method="post">
  <h2>Propriedades do Relat&oacute;rio a ser Gerado:</h2>
  <?php

//Cen�rio - Gerar Relat�rios XML 

//Objetivo:    Permitir ao administrador gerar relat�rios em formato XML de um projeto,
//          identificados por data.     
//Contexto:    Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//          Pr�-Condi��o: Login, projeto cadastrado.
//Atores:    Administrador     
//Recursos:    Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.     
//Epis�dios:O administrador clica na op��o de Gerar Relat�rio XML.
//          Restri��o: Somente o Administrador do projeto pode ter essa fun��o vis�vel.
//          O sistema fornece para o administrador uma tela onde dever� fornecer os dados
//          do relat�rio para sua posterior identifica��o, como data e vers�o. 

   $today = getdate(); 
?>
  &nbsp;Data da Vers&atilde;o:
  <?= $today['mday'];?>
  /
  <?= $today['mon'];?>
  /
  <?= $today['year'];?>
  <p>&nbsp;
    <input type="hidden" name="data_dia" size="3" value="<?= $today['mday'];?>">
    <input  type="hidden" name="data_mes" size="3" value="<?= $today['mon'];?>">
    <input type="hidden" name="data_ano" size="6" value="<?= $today['year'];?>">
    &nbsp;</p>
  Vers&atilde;o do XML: &nbsp;
  <input type="text" name="versao" size="15">
  <p>Exibir
    
    Formatado:
    <input type="checkbox" name="flag" value="ON">
    <br>
    <br>
    <input type="submit" value="Gerar">
  </p>
</form>
<br>
<i><a href="showSource.php?file=form_xml.php">Veja o c&oacute;digo fonte!</a></i>
</body>
</html>
