<?php

include_once ("../aplicacao/bd.inc");
include_once ("../aplicacao/bd_class.php");
include_once("../aplicacao/seguranca.php");

// Return TRUE if $id_usuario is admin of $id_projeto
if (!(function_exists("is_admin")))
{
    function is_admin($id_usuario, $id_projeto)
    {
    	assert($id_usuario != null, "id_usuario must not be null");
    	assert($id_projeto != null, "id_projeto must not be null");
    	
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $query = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $id_projeto
              AND gerente = 1";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        if (mysql_num_rows($queryResult) == 1)
        {
                $is_admin = true;
        }
        else
        {
                $is_admin = false;    
        }
        
        return $is_admin;
    }
}
else
{
	//Nothing to do.
}


?>