<?php 

    //session_start(); 

    //include("funcoes_genericas.php"); 

    chkUser("index.php");        // Cenario: controle de acesso 

// frame_inferior.php 
// Dada a base, o tipo "c", "l", "oc", "or" e "oa" e o 
// id do respectivo, mostra os dados necessários 
// no frame. 

    function frame_inferior( $bd, $tipo, $id  ) 
    { 
        $search = "'<[\/\!]*?[^<>]*?>'si"; 
        $replace = ""; 


        if ( $tipo == "c" )            // Se for cenario 
        { 
            // Seleciona os cenários que referenciam o cenário 
            // com o id passado. 
            $qry_cenario = "SELECT id_cenario, titulo 
                            FROM   cenario, centocen 
                            WHERE  id_cenario = id_cenario_from 
                            AND    id_cenario_to = " . $id ; 

            $tb_cenario = mysql_query( $qry_cenario ) or 
                          die( "Erro ao enviar a query de selecao." ) ; 
?> 

            <table> 
            <tr> 
              <th>Cenários</th> 
            </tr> 

<?php 
            while ( $row = mysql_fetch_row( $tb_cenario ) ) 
            { 
                // Retira as tags HTML de dentro do titulo do cenario 
                $row[1] = preg_replace($search, $replace, $row[1]); 
                $link = "<a href=javascript:reCarrega" . 
                        "('main.php?id=$row[0]&t=c');><span style=\"font-variant: small-caps\">$row[1]</span></a>" ; 
?> 

                <td><?=$link?></td> 

<?php 
            } // while 
        } // if 

        else if ( $tipo == "l" ) 
        { 
            // Seleciona os cenários que referenciam o léxico 
            // com o id passado. 
            $qry_cenario = "SELECT c.id_cenario, c.titulo 
                            FROM   cenario c, centolex cl 
                            WHERE  c.id_cenario = cl.id_cenario 
                            AND    cl.id_lexico = " . $id ; 

            $tb_cenario = mysql_query( $qry_cenario ) or 
                          die( "Erro ao enviar a query de selecao." ) ; 

            // Seleciona os lexicos que referenciam o lexico 
            // com o id passado. 
            $qry_lexico = "SELECT id_lexico, nome 
                           FROM   lexico, lextolex 
                           WHERE  id_lexico  = id_lexico_from 
                           AND    id_lexico_to = " . $id ; 

            $tb_lexico = mysql_query( $qry_lexico ) or 
                         die( "Erro ao enviar a query de selecao." ) ; 
?> 

            <table> 
            <tr> 
                <th>Cenários</th> 
                <th>Léxicos</th> 
            </tr> 

<?php 
            while ( 1 ) 
            { 
?> 

                <tr> 

<?php 
                if ( $rowc = mysql_fetch_row( $tb_cenario ) ) 
                { 
                    $rowc[1] = preg_replace($search, $replace, $rowc[1]); 
                    $link = "<a href=javascript:reCarrega" . 
                            "('main.php?id=$rowc[0]&t=c');><span style=\"font-variant: small-caps\">$rowc[1]</span></a>" ; 
                } // if 
                else 
                { 
                    $link = "" ; 
                } // else 
?> 

                <td><?=$link?></td> 

<?php 
                if ( $rowl = mysql_fetch_row( $tb_lexico ) ) 
                { 
                    $link = "<a href=javascript:reCarrega" . 
                            "('main.php?id=$rowl[0]&t=l');>$rowl[1]</a>" ; 
                } // if 
                else 
                { 
                    $link = "" ; 
                } // else 
?> 

                <td><?=$link?></td> 
</td>                </tr> 

<?php 
                if ( !( $rowc ) && !( $rowl ) ) 
                { 
                    break ; 
                } // if 
            } // while 

        } //elseif 

        else if ( $tipo == "oc" ) /* CONCEITO */ 
        { 
           $q = "SELECT   r.id_relacao, r.nome, predicado 
                 FROM     conceito c, relacao_conceito rc, relacao r 
                 WHERE    c.id_conceito = $id 
                 AND      c.id_conceito = rc.id_conceito 
                 AND      r.id_relacao = rc.id_relacao 
                 ORDER BY r.nome  ";  
           $result = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());  
        
           print "<TABLE><tr><th align=left CLASS=\"Estilo\">Relação</th><th align=left CLASS=\"Estilo\">Conceito</Th></tr>"; 

           while ($line = mysql_fetch_array($result, MYSQL_BOTH))   
           { 
              print "<tr><td CLASS=\"Estilo\"><a href=\"main.php?id=$line[0]&t=or\">$line[1]</a></td><td>$line[2]</TD></tr>"; 
           } 
            


        } //elseif 

        else if ( $tipo == "or" ) /* RELAÇÃO */ 
        { 
           $q = "SELECT DISTINCT  c.id_conceito, c.nome 
                 FROM     conceito c, relacao_conceito rc, relacao r 
                 WHERE    r.id_relacao = $id 
                 AND      c.id_conceito = rc.id_conceito 
                 AND      r.id_relacao = rc.id_relacao 
                 ORDER BY r.nome  ";  
           $result = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());  
        
           print "<TABLE><tr><th align=left CLASS=\"Estilo\">Conceitos</th></tr>"; 

           while ($line = mysql_fetch_array($result, MYSQL_BOTH))   
           { 
              print "<tr><td CLASS=\"Estilo\"><a href=\"main.php?id=$line[0]&t=oc\">$line[1]</a></td></tr>"; 
           } 
            


        } //elseif 

        else if ( $tipo == "oa" ) /* AXIOMA */ 
        { 

           $q = "SELECT   * 
                 FROM     axioma
                 WHERE    id_axioma = \"$id\";";  

           $result = mysql_query($q) or die("Erro ao enviar a query de selecao !!". mysql_error());  
        
           print "<TABLE><tr><th align=left CLASS=\"Estilo\">Conceito</th><th align=left CLASS=\"Estilo\">Conceito disjunto</th></tr>"; 

           while ($line = mysql_fetch_array($result, MYSQL_BOTH))   
           { 
              $axi = explode(" disjoint ", $line[1]);     

              $q1 = "SELECT * FROM conceito WHERE nome = \"$axi[0]\";";          
              $result1 = mysql_query($q1) or die("Erro ao enviar a query de selecao !!". mysql_error());  
              $line1 = mysql_fetch_array($result1, MYSQL_BOTH) ; 
              print "<tr><td CLASS=\"Estilo\"><a href=\"main.php?id=$line1[0]&t=oc\">$axi[0]</a></td>";

              $q2 = "SELECT * FROM conceito WHERE nome = \"$axi[1]\";";          
              $result2 = mysql_query($q2) or die("Erro ao enviar a query de selecao !!". mysql_error());  
              $line2 = mysql_fetch_array($result2, MYSQL_BOTH) ; 
              print "<td CLASS=\"Estilo\"><a href=\"main.php?id=$line2[0]&t=oc\">$axi[1]</a></td></tr>"; 
           } 
            


        } //elseif 
        
?> 

</table> 

<?php 
    } // procura_ref 
?>
