<?php

include ("../aplicacao/funcoes_genericas.php");
include ("../aplicacao/dataBase/DatabaseProject.php");

class DataBaseIsAdminTest extends PHPUnit_Framework_TestCase
{
    public function testIsAdmin()
    {
        $id_projeto = includeProject("Nome Teste", "Descrição teste");
        $id_usuario = 10;
        
        $retorno = is_admin($id_usuario, $id_projeto);
        $this->assertEquals(false, $retorno);
        removeProject($idProject);
    }
    
}

?>
