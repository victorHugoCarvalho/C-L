<?php

session_start();

include("funcoes_genericas.php");

checkUser("index.php");        // Checa se o usuario foi autenticado

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

// Scenery - Generate XML Reports

// Purpose: Allow the administrator to generate reports in XML format to a project identified by date.
// Context: Manager to generate a report for a project which is administrator.
// Precondition: Login, registered design.
// Actors: Administrator
// Resources: System, report data, data registered design, database.
// Episodes: The administrator clicks the option Generate XML Report.
// Restriction: Only the Project Manager may have this function visible.
// The system provides for a screen where the administrator must provide the report 
// data for subsequent identification, such as date and version.

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
