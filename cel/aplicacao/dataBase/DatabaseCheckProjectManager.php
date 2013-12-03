<?php
include_once ("../aplicacao/bd.inc");
include_once ("../aplicacao/bd_class.php");
include_once("../aplicacao/seguranca.php");

###################################################################
# Verifica se um determinado usuario e gerente de um determinado
# projeto
# Recebe o id do projeto. (1.1)
# Faz um select para pegar o resultArray da tabela Participa.(1.2)
# Se o resultArray for nao nulo: devolvemos TRUE(1);(1.3)
# Se o resultArray for nulo: devolvemos False(0);(1.4)
###################################################################
function check_project_manager($id_usuario, $id_projeto)
{
	assert($id_usuario != null, "id_usuario must not be null");
	assert($id_projeto != null, "id_projeto must not be null");
	
    $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
    $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($queryResult);
    
    if ($resultArray != false)
    {
        $manager = 1;
    }
    else
    {
    	$manager = 0;
    }
    
    return $manager;
}

?>