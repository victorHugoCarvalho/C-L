<?php

include ("../aplicacao/funcoes_genericas.php");
include ("../aplicacao/dataBase/DatabaseProject.php");

class DataBaseCheckProejctManagerTest extends PHPUnit_Framework_TestCase
{
    public function testCheckProjectManagerReturnTrue()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_newProject = includeProject("Nome Teste", "Descrição teste");
        $id_user = 10;
        $manager = 1;
        
        $query = "INSERT INTO participa (id_usuario, id_projeto, gerente) 
                  VALUES ($id_user, $id_newProject, $manager)";
        
        $check = check_project_manager($id_user, $id_newProject);
        $this->assertEquals(0, $check);
        
        removeProject($id_newProject);
    }
    
    public function testCheckProjectManagerReturnFalse()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        $id_newProject = includeProject("Nome Teste", "Descrição teste");
        $id_user = 10;
        $manager = 0;
        
        $query = "INSERT INTO participa (id_usuario, id_projeto, gerente) 
                  VALUES ($id_user, $id_newProject, $manager)";
        
        $check = check_project_manager($id_user, $id_newProject);
        $this->assertEquals(0, $check);
        
        removeProject($id_newProject);
    }
}
