<?php

include ("../aplicacao/dataBase/DatabaseProject.php");
include ("../aplicacao/dataBase/DatabaseIsAdmin.php");

###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeCenario")))
{
    function removeCenario($id_projeto, $id_cenario)
    {
    	assert($id_projeto != null, "id_projeto must not be null");
    	assert($id_cenario != null, "id_cenario must not be null");
    	
        $DB = new PGDB () ;
        $sql1 = new QUERY ($DB) ;
        $sql2 = new QUERY ($DB) ;
        $sql3 = new QUERY ($DB) ;
        $sql4 = new QUERY ($DB) ;
               
        # Remove o relacionamento entre o cenario a ser removido
        # e outros cenarios que o referenciam
        $sql1->execute ("DELETE FROM centocen WHERE id_cenario_from = $id_cenario") ;
        $sql2->execute ("DELETE FROM centocen WHERE id_cenario_to = $id_cenario") ;
        # Remove o relacionamento entre o cenario a ser removido
        # e o seu lexico
        $sql3->execute ("DELETE FROM centolex WHERE id_cenario = $id_cenario") ;
        # Remove o cenario escolhido
        $sql4->execute ("DELETE FROM cenario WHERE id_cenario = $id_cenario") ;
    }
}
else
{
	//Nothing to do.
}




?>