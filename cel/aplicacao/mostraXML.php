<?php

session_start();
include("funcoes_genericas.php");
include("httprequest.inc");

chkUser("index.php");        // checks whether the user has been authenticated 
   
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

// Scenery - Generate XML Reports

// Purpose: Allow the administrator to generate reports in XML format to a project,
// Identified by date.
// Context: Manager to generate a report for a project which is administrator.
// Precondition: Login, registered design.
// Actors: Administrator
// Resources: System, report data, data registered design, database.
// Episodes: Generating Success with the report from the data registered design,
// The system gives the administrator the viewing screen of the report
// XML created.
   
$query = "select * from publicacao where id_projeto = $id_projeto AND versao = $versao";
$ExecuteQuery = mysql_query($query) or die("Erro ao enviar a query");
$row = mysql_fetch_row($ExecuteQuery);
$xml_banco = $row[3];

echo $xml_banco;
	
?>
