<?php

include ("../aplicacao/funcoes_genericas.php");
include ("../aplicacao/dataBase/DatabaseProject.php");
include ("../aplicacao/dataBase/DatabaseScenario.php");

class databaseLexyconTest extends PHPUnit_Framework_TestCase
{

//	public function testBase()
//    {
//        $_SESSION['id_usuario_corrente'] = "Teste";
//        ob_start();
//        
//        require_once ("..cel/aplicacao/dataBase/DatabaseProject.php");
//        require_once ("..cel/aplicacao/dataBase/DatabaseScenario.php");
//        require_once ("..cel/aplicacao/funcoes_genericas.php");
//        
//        $idProject = includeProject("Projeto Teste", "Descrição");
//        
//        return ob_get_clean();
//    }
	
	/*
	 * @test
	 */
	public function testCheckLexiconExistsReturnFalse() 
	{
            $idProject = includeProject("Nome projeto", "Descrição");
            removeProject($idProject);

            $lexiconExists = checkLexiconExists($idProject, "Léxico inexistente");

            $this->assertFalse($lexiconExists);
	}
        
        public function testCheckLexiconExistsReturnTrue()
        {
            $_SESSION['id_usuario_corrente'] = "Teste";
            $id_newProject = includeProject("Nome Teste", "Descrição teste");
            $name = "Test name";

            $queryLexico = "INSERT INTO lexico (id_projeto, nome) VALUES ($id_user, $id_newProject)";
            mysql_query($queryLexico);
            $Sinonimo = "INSERT INTO sinonimo (nome, id_projeto) VALUES ($name, $id_newProject)";
            mysql_query($Sinonimo);
            
            $checkLexico = checkLexiconExists($id_newProject, $name);

            $queryResultDeleteLexico = "DELETE FROM lexico id_projeto = $id_newProject";
            mysql_query($queryResultDeleteLexico);
            $queryResultDeleteSinonimo = "DELETE FROM sinonimo id_projeto = $id_newProject";
            mysql_query($queryResultDeleteSinonimo);
            
            removeProject($id_newProject);

            $this->assertTrue($check);
        }
}

?>
