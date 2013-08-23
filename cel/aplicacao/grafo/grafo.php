<html>
<head>
</head>
<body bgcolor="white">
<center>
<? 
	extract($_GET);
	if (!isset($xmlSource)) {
		echo "Erro! Nenhum arquivo XML foi declarado.<br /><br /><strong>Sintaxe</strong>: grafo.php?xmlSource=<i>ArquivoXML.xml</i>";
	} else if (!file_exists($xmlSource)) {
		echo "Erro! O arquivo XML <strong>".$xmlSource."</strong> não foi encontrado.";
	} else {
?>
		<applet code="gapp/GApp.class" width="100%" height="100%">
			<param name="xmlSource" value="<? echo $xmlSource ?>" />
		</applet>
<?
	}
?>
</center>
</body>
</html>