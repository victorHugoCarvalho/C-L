<?php

include_once ("../aplicacao/bd.inc");
include_once ("../aplicacao/bd_class.php");
include_once("../aplicacao/seguranca.php");


// Retorna TRUE se $id_usuario tem permissao sobre $id_projeto
if (!(function_exists("check_proj_perm")))
{
    function check_project_permission($id_usuario, $id_projeto)
    {
    	assert($id_usuario != null, "id_usuario must not be null");
    	assert($id_projeto != null, "id_projeto must not be null");
    	
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $query = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        if (mysql_num_rows($queryResult) == 1)
        {
            $permission = true;
        }
        else 
        {
            $permission = false;
        }
        
        return $permission;
    }
}
else
{
	//Nothing to do.
}


?>