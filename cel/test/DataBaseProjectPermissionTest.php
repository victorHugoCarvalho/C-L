<?php

include ("../aplicacao/dataBase/DatabaseProject.php");
include ("../aplicacao/dataBase/DatabaseProjectPermission.php");

class DataBaseProjectPermissionTest extends PHPUnit_Framework_TestCase
{
    public function testProjectPermissionReturnFalse()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_user = 10;
        $id_newProject = includeProject("Nome Teste", "Descrição teste");
        
        $check = check_project_permission($id_user, $id_newProject);
        $this->assertEquals(false, $check);
        removeProject($id_newProject);
    }
    
    public function testProjectPermissionReturnTrue()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_newProject = includeProject("Nome Teste", "Descrição teste");
        $id_user = 10;
        
        $query = "INSERT INTO participa (id_usuario, id_projeto) 
                  VALUES ($id_user, $id_newProject)";
        
        mysql_query($query);
        $check = check_project_permission($id_user, $id_newProject);
        
        $queryResultDelete = "DELETE FROM participa id_projeto = $id_newProject";
        mysql_query($queryResultDelete);
        removeProject($id_newProject);
        
        $this->assertTrue($check);
    }
}

?>
