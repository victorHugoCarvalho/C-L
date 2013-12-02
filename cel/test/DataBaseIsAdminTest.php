<?php

include ("../aplicacao/funcoes_genericas.php");
include ("../aplicacao/dataBase/DatabaseProject.php");

class DataBaseIsAdminTest extends PHPUnit_Framework_TestCase
{
    public function testIsAdminReturnFalse()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_newProject = includeProject("Nome Teste", "Descrição teste");
        $id_user = 10;
        
        $check = is_admin($id_user, $id_newProject);
        $this->assertEquals(false, $check);
        removeProject($id_newProject);
    }
    
    public function testIsAdminReturnTrue()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_newProject = includeProject("Nome Teste", "Descrição teste");
        $id_user = 10;
        $admin = 1;
        
        $query = "INSERT INTO participa (id_usuario, id_projeto, gerente) 
            VALUES ($id_user, $id_newProject, $admin)";
        
        mysql_query($query);
        
        $check = is_admin($id_user, $id_newProject);
        
                $queryResultDelete = "DELETE FROM participa 
        WHERE gerente = $admin AND id_projeto = $id_newProject";
        mysql_query($queryResultDelete);
        removeProject($id_newProject);
        
        $this->assertTrue($check);
        

    }
    
    public function testProjectPermissionReturnTrue()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_user = 10;
        $id_newProject = includeProject("Nome Teste", "Descrição teste");
        
        $check = check_project_permission($id_user, $id_newProject);
        $this->assertEquals(false, $check);
        removeProject($id_newProject);
    }
}

?>
