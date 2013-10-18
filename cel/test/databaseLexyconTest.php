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
	public function testCheckLexiconExistsWithExistingLexicon() 
	{
        $idProject = includeProject("Nome projeto", "Descrição");
        removeProject($idProject);
        
        $lexiconExists = checkLexiconExists($idProject, "Léxico inexistente");
        
        $this->assertFalse($lexiconExists);
	}
}

?>
