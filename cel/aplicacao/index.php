<?php 

session_start(); 
include("funcoes_genericas.php"); 
chkUser("index.php");        // Cenario: controle de acesso 

?> 

<title>C&L - Cenários e Léxico</title> 
<frameset rows="103,*" cols="*" frameborder="NO" border="0" framespacing="0"> 
    <frame src="heading.php" name="heading" scrolling="NO"> 
    <frameset cols="160,40,*" frameborder="NO" border="0" framespacing="0" rows="*"> 
        <frameset rows="0,*" frameborder="NO" border="0" framespacing="0" rows="*"> 
            <frame src="code.php" name="code"> 
            <frame src="menu_empty.htm" name="menu"> 
        </frameset> 
        <frame src="VertBar.htm" name="VertBar"> 
        <frame src="main.php" name="text"> 
    </frameset> 
</frameset> 
