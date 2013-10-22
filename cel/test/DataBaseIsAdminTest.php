<?php

include ("../aplicacao/funcoes_genericas.php");
include ("../aplicacao/dataBase/DatabaseProject.php");

class DataBaseIsAdminTest extends PHPUnit_Framework_TestCase
{
    public function testIsAdminReturnFalse()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_projeto = includeProject("Nome Teste", "Descrição teste");
        $id_usuario = 10;
        
        $retorno = is_admin($id_usuario, $id_projeto);
        $this->assertEquals(false, $retorno);
        removeProject($id_projeto);
    }
    
    public function testIsAdminReturnTrue()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_projeto = includeProject("Nome Teste", "Descrição teste");
        $id_usuario = 10;
        $gerente = 1;
        
        $queryResult = "INSERT INTO participa (id_usuario, id_projeto,gerente) 
            VALUES ($id_projeto, $id_usuario,$gerente)";
        
        mysql_query($queryResult);
        
        $retorno = is_admin($id_usuario, $id_projeto);
        $this->assertTrue($retorno);
        
        $queryResultDelete = "DELETE FROM participa 
        WHERE gerente = $gerente AND id_usuario = $id_projeto AND id_projeto = $id_projeto";
        mysql_query($queryResultDelete);
        removeProject($id_projeto);
    }
}

?>
