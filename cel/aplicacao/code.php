<?php  

session_start();  


if (isset($_GET['id_projeto']))  
{  
    $id_projeto = $_GET['id_projeto'];  
}
else
{
	//Nothing to do.
}  


include("funcoes_genericas.php");  
include_once("bd.inc");

checkUser("index.php");        // Checa se o usuario foi autenticado 


//$id_projeto = 2; 

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php  

// conecta ao SGBD 
$sgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");  

// A variavel $id_projeto, se estiver setada, corresponde ao id do projeto que 
// devera ser mostrado. Se ela nao estiver setada entao, por default, 
// nao mostraremos projeto algum (esperaremos o usuario escolher um projeto). 
// Como a passagem eh feita usando JavaScript (no heading.php), devemos checar 
// se este id realmente corresponde a um projeto que o usuario tenha acesso 
// (seguranca). 

if (isset($id_projeto))
{  
    check_proj_perm($_SESSION['id_usuario_corrente'], $id_projeto) or die("Permiss&atilde;o negada");  
    $q = "SELECT nome FROM projeto WHERE id_projeto = $id_projeto";  
    $qrr = mysql_query($q) or die("Erro ao enviar a query");  
    $result = mysql_fetch_array($qrr);  
    $nome_projeto = $result['nome'];  
}
else
{  

?>
<script language="javascript1.3"> 
	top.frames['menu'].document.writeln('<font color="red">Nenhum projeto selecionado</font>'); 
</script>
<?php  

    exit();  
}  

?>
<html>
<head>
<script type="text/javascript"> 
// Framebuster script to relocate browser when MSIE bookmarks this 
// page instead of the parent frameset.  Set variable relocateURL to 
// the index document of your website (relative URLs are ok): 
/*var relocateURL = "/"; 

if (parent.frames.length == 0) { 
    if(document.images) { 
        location.replace(relocateURL); 
    } else { 
        location = relocateURL; 
    } 
}*/ 
</script>
<script type="text/javascript" src="mtmcode.js"> 
</script>
<script type="text/javascript"> 
// Morten's JavaScript Tree Menu 
// version 2.3.2, dated 2002-02-24 
// http://www.treemenu.com/ 

// Copyright (c) 2001-2002, Morten Wang & contributors 
// All rights reserved. 

// This software is released under the BSD License which should accompany 
// it in the file "COPYING".  If you do not have this file you can access 
// the license through the WWW at http://www.treemenu.com/license.txt 

// Nearly all user-configurable options are set to their default values. 
// Have a look at the section "Setting options" in the installation guide 
// for description of each option and their possible values. 

MTMDefaultTarget = "text"; 
MTMenuText = "<?=$nome_projeto?>"; 

/****************************************************************************** 
* User-configurable list of icons.                                            * 
******************************************************************************/ 

var MTMIconList = null; 
MTMIconList = new IconList(); 
MTMIconList.addIcon(new MTMIcon("menu_link_external.gif", "http://", "pre")); 
MTMIconList.addIcon(new MTMIcon("menu_link_pdf.gif", ".pdf", "post")); 

/****************************************************************************** 
* User-configurable menu.                                                     * 
******************************************************************************/ 

var menu = null; 
menu = new MTMenu(); 
menu.addItem("Cen&aacute;rios"); 
// + submenu 
var mc = null; 
mc = new MTMenu(); 

<?php  
$q = "SELECT id_cenario, titulo  
	  FROM cenario  
      WHERE id_projeto = $id_projeto  
      ORDER BY titulo";  

$qrr = mysql_query($q) or die("Erro ao enviar a query de sele&ccedil;&atilde;o");  
// Devemos retirar todas as tags HTML do titulo do cenario. Possivelmente 
// havera tags de links (<a> </a>). Caso nao tiremos, havera erro ao 
// mostra-lo no menu. Este search & replace retira qualquer coisa que 
// seja da forma <qualquer_coisa_aqui>. Pode, inclusive, retirar trechos 
// que nao sao tags HTML. 
$search = "'<[\/\!]*?[^<>]*?>'si";  
$replace = "";

while ($row = mysql_fetch_row($qrr)) // para cada cenario do projeto 
{    
    $row[1] = preg_replace($search, $replace, $row[1]);  
	?>  
	
	mc.addItem("<?=$row[1]?>", "main.php?id=<?=$row[0]?>&t=c"); 
	
	// + submenu 
	var mcs_<?=$row[0]?> = null; 
	mcs_<?=$row[0]?> = new MTMenu(); 
	mcs_<?=$row[0]?>.addItem("Sub-cen&aacute;rios", "", null, "Cen&aacute;rios que este cen&aacute;rio refer&ecirc;ncia"); 
	// + submenu 
	var mcsrc_<?=$row[0]?> = null; 
	mcsrc_<?=$row[0]?> = new MTMenu(); 
	
	<?php  
    $q = "SELECT c.id_cenario_to, cen.titulo FROM centocen c, cenario cen WHERE c.id_cenario_from = " . $row[0];  
    $q = $q . " AND c.id_cenario_to = cen.id_cenario";  
    $qrr_2 = mysql_query($q) or die("Erro ao enviar a query de selecao");  
    
    while ($row_2=mysql_fetch_row($qrr_2))
	{  
		$row_2[1] = preg_replace($search, $replace, $row_2[1]);  
		?>  
		
		mcsrc_<?=$row[0]?>.addItem("<?=$row_2[1]?>", "main.php?id=<?=$row_2[0]?>&t=c&cc=<?=$row[0]?>"); 
		
		<?php  
    }  
		?>  

	// - submenu 
	mcs_<?=$row[0]?>.makeLastSubmenu(mcsrc_<?=$row[0]?>); 
	
	// - submenu 
	mc.makeLastSubmenu(mcs_<?=$row[0]?>); 
	
	<?php  
}
	?>  

// - submenu 
menu.makeLastSubmenu(mc); 

menu.addItem("L&eacute;xico"); 
// + submenu 
var ml = null; 
ml = new MTMenu(); 

<?php  
$q = "SELECT id_lexico, nome  
      FROM lexico  
      WHERE id_projeto = $id_projeto  
      ORDER BY nome";  

$qrr = mysql_query($q) or die("Erro ao enviar a query de sele&ccedil;&atilde;o");  
while ($row=mysql_fetch_row($qrr))   // para cada lexico do projeto
{ 
	?>  
	
	ml.addItem("<?=$row[1]?>", "main.php?id=<?=$row[0]?>&t=l"); 
	// + submenu 
	var mls_<?=$row[0]?> = null; 
	mls_<?=$row[0]?> = new MTMenu(); 
	// mls_<?=$row[0]?>.addItem("L&eacute;xico", "", null, "Termos do l&eacute;xico que este termo refer&ecirc;ncia"); 
	// + submenu 
	// var mlsrl_<?=$row[0]?> = null; 
	// mlsrl_<?=$row[0]?> = new MTMenu(); 
	
	<?php  
    $q = "SELECT l.id_lexico_to, lex.nome FROM lextolex l, lexico lex WHERE l.id_lexico_from = " . $row[0];  
    $q = $q . " AND l.id_lexico_to = lex.id_lexico";  
    $qrr_2 = mysql_query($q) or die("Erro ao enviar a query de selecao");  
    while ($row_2=mysql_fetch_row($qrr_2))
	{  
	?>  
	
	// mlsrl_<?=$row[0]?>.addItem("<?=$row_2[1]?>", "main.php?id=<?=$row_2[0]?>&t=l&ll=<?=$row[0]?>"); 
	mls_<?=$row[0]?>.addItem("<?=$row_2[1]?>", "main.php?id=<?=$row_2[0]?>&t=l&ll=<?=$row[0]?>"); 
	
	<?php  
	}  
	?>  

	// - submenu 
	// mls_<?=$row[0]?>.makeLastSubmenu(mlsrl_<?=$row[0]?>); 
	// - submenu 
	ml.makeLastSubmenu(mls_<?=$row[0]?>); 

	<?php  
}
	?>  

// -submenu 
menu.makeLastSubmenu(ml); 


// ONTOLGIA 
// + submenu 
menu.addItem("Ontologia"); 
var mo = null; 
mo = new MTMenu(); 

// -submenu 
menu.makeLastSubmenu(mo); 


// CONCEITO 
// ++ submenu 
   mo.addItem("Conceitos"); 
   var moc = null; 
   moc = new MTMenu(); 

<?php  
    $q = "SELECT id_conceito, nome  
          FROM conceito 
          WHERE id_projeto = $id_projeto  
          ORDER BY nome";  

    $qrr = mysql_query($q) or die("Erro ao enviar a query de sele&ccedil;&atilde;o");  
    while ($row=mysql_fetch_row($qrr))  // para cada conceito do projeto 
    {   
        print "moc.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=oc\");"; 
    }  
?>  

// --submenu 
   mo.makeLastSubmenu(moc); 




// RELA��ES 
// ++ submenu 
   mo.addItem("Rela&ccedil;&otilde;es"); 
   var mor = null; 
   mor = new MTMenu(); 

<?php  
    $q = "SELECT   id_relacao, nome 
          FROM     relacao r 
          WHERE    id_projeto = $id_projeto  
          ORDER BY nome";  

    $qrr = mysql_query($q) or die("Erro ao enviar a query de sele&ccedil;&atilde;o");  
    while ($row=mysql_fetch_row($qrr))   // para cada rela��o do projeto 
    {    
        print "mor.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=or\");"; 
    } 
?> 

// --submenu    
   mo.makeLastSubmenu(mor); 




// AXIOMAS 
// ++ submenu 
   mo.addItem("Axiomas"); 
   var moa = null; 
   moa = new MTMenu(); 

<?php  
   $q = "SELECT   id_axioma, axioma 
         FROM     axioma 
         WHERE    id_projeto = $id_projeto  
         ORDER BY axioma";  

   $qrr = mysql_query($q) or die("Erro ao enviar a query de sele&ccedil;&atilde;o");  

   while ($row=mysql_fetch_row($qrr))  // para cada axioma do projeto 
   {  
      $axi = explode(" disjoint ", $row[1]);      
      print "moa.addItem(\"$axi[0]\", \"main.php?id=$row[0]&t=oa\");";  
   } 
?> 

// --submenu    
   mo.makeLastSubmenu(moa); 



</script>
</head>
<body onload="MTMStartMenu(true)" bgcolor="#000033" text="#ffffcc" link="yellow" vlink="lime" alink="red">
</body>
</html>
