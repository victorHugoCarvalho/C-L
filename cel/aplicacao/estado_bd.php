<?php

include_once("bd.inc"); 

$link = bd_connect();

$query = "show tables" ;
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 


    print "<font color=#7c75b2 face=arial><h3>TABELAS e seus ATRIBUTOS<h3></font>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) 
{ 
    print "<table border=1><tr><td bgcolor=#7c75b2 width=120><font color=white>". $line[0] . "</font></td>";
    $tabela = "describe " . $line[0] ;
    $atributos = mysql_query($tabela) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
    while ($linha = mysql_fetch_array($atributos, MYSQL_BOTH)) 
    {  
       print  "<td>" . $linha[0] . " </td>";
    }
    print "</tr></table><br>";
} 




/* PROJETO que está sendo traduzido pelo Jerônimo (Adm_Imoveis)*/

$projetos = "select * from projeto where nome='Adm_Imoveis' order by id_projeto" ;
$resultado = mysql_query($projetos) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Projeto que está sendo traduzido pelo Jerônimo<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>data da criação</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>descrição</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>id_status</font></td>
           </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>". $line[4] . "</td><td>". $line[5] . "</td><td>";
  }
  print "</table>";

  /* PEDIDOREL */

$resultados = "select * from pedidorel order by nome" ;
$resultado = mysql_query($resultados) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>PedidoRel<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_pedido</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>id_usuario</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>tipo_pedido</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>aprovado</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>id_relacao</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>justificativa</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>id_status</font></td>
  
           </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>" . $line[4] . "</td><td>" . $line[5] . "</td><td>" . $line[6] . "</td><td>" . $line[7] . "</td><td>" . $line[8] . "</td><td>";
  }
  print "</table>";

/* LEXICO */

$resultados = "select * from lexico order by nome" ;
$resultado = mysql_query($resultados) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Lexico<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_lexico</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>data</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>tipo</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>nocao</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>impacto</font></td>
  
           </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>" . $line[4] . "</td><td>" . $line[5] . "</td><td>" . $line[6] . "</td><td>" . $line[7] . "</td><td>";
  }
  print "</table>";
    
  
/* ALGORITMO */

$resultados = "select * from algoritmo" ;
$resultado = mysql_query($resultados) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Algoritmo<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_variavel</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>valor</font></td>                  
           </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>";
  }
  print "</table>";

/* CONCEITOS */

$conceitos = "select * from conceito order by nome asc" ;
$resultado = mysql_query($conceitos) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Conceitos<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_conceito</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>descricao</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>pai</font></td>
           </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>". $line[4] . "</td><td>". $line[5] . "</td><td>";
  }
  print "</table>";




/* RELAÇÕES */

$relacoes = "select * from relacao order by id_relacao" ;
$resultado = mysql_query($relacoes) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Relações<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_relacao</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
         </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>";
  }
  print "</table>";


/* HIERARQUIA */

$hierarquia = "select * from hierarquia" ;
$resultado = mysql_query($hierarquia) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Hierarquia<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_hierarquia</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>id_conceito</font></td>
			 <td bgcolor=#7c75b2 width=120><font color=white>id_subconceito</font></td>
         </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>";
  }
  print "</table>";



/* RELAÇÕES ENTRE CONCEITOS */

$rc = "select c.nome, r.nome, rc.predicado, rc.id_projeto from relacao_conceito rc, relacao r, conceito c WHERE c.id_conceito = rc.id_conceito AND rc.id_relacao = r.id_relacao ORDER BY c.nome, r.nome ASC;" ;
$resultado = mysql_query($rc) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Relação entre conceitos<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>conceito</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>relacao</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>predicado</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
         </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>";
  }
  print "</table>";




/* AXIOMAS */

$axiomas = "select * from axioma order by id_axioma" ;
$resultado = mysql_query($axiomas) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Axiomas<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_axioma</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>axioma</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
         </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>";
  }
  print "</table>";


/* USUÁRIOS */

$usuarios = "select * from usuario order by id_usuario" ;
$resultado = mysql_query($usuarios) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Usuários<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_usuario</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>email</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>login</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>senha</font></td>
         </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" .  $line[3] . "</td><td>" . $line[4] . "</td><td>";
  }
  print "</table>";


/* PARTICIPA */

$participa = "select * from participa order by id_projeto" ;
$resultado = mysql_query($participa) or die("A consulta à BD falhou : " . mysql_error() . __LINE__); 
  print "<br><br><font color=#7c75b2 face=arial><h3>Participa<h3></font>";
  print "<table border=1>";
  print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_usuario</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>gerente</font></td>
         </tr>";

  while ($line = mysql_fetch_array($resultado, MYSQL_BOTH)) 
  {  
     print "<tr><td>". $line[0] . "</td><td>". $line[1] . "</td><td>" . $line[2] . "</td><td>" ;
  }
  print "</table>";



mysql_close($link);

?>