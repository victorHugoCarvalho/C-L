<?php

require_once ("../aplicacao/dataBase/DatabaseScenario.php");
require_once ("../aplicacao/funcoes_genericas.php");
require_once ("../aplicacao/dataBase/DatabaseProject.php");

class DatabaseScenarioTest extends PHPUnit_Framework_TestCase
{
    
    /*
    @test
    */
    public function testCheckScenarioExistsReturnFalse()
    {
        
        $project = "1";
        $title = "Texto";
        $result = checkScenarioExists($project, $title);

        $this->assertFalse($result);
    }
    /*
    @test
    */
    public function testCheckScenarioExistsReturnTrue()
    {
        
        $id_newProject = includeProject("Projeto Teste", "teste");
        $title = "Texto";
        inclui_cenario($id_newProject, $title, "Texto", "Texto", "Texto", "Texto", "Texto", "Texto");
        
        $result = checkScenarioExists($id_newProject, $title);

        $this->assertTrue($result);
        
        removeProject($id_newProject);
    }
    
    /*
     @test
    */
    public function testCheckScenarioExistsReturnTrue()
    {
    
    	$id_newProject = includeProject("Projeto Teste", "teste");
    	$title = "Texto";
    	inclui_cenario($id_newProject, $title, "Texto", "Texto", "Texto", "Texto", "Texto", "Texto");
    
    	$result = checkScenarioExists($id_newProject, $title);
    
    	$this->assertTrue($result);
    
    	removeProject($id_newProject);
    }
    
    /*
     @test
    */
    public function testInclui_cenario()
    {
    	$id_newProject = includeProject("Projeto Teste", "teste");
    	$title = "Texto";
    	$objective = "Texto";
    	$context = "Texto";
    	$actors = "Texto";
    	$resources = "Texto";
    	$exception = "Texto";
    	$episodes = "Texto";
    
    	$id_incluido = inclui_cenario($id_newProject, $title, $objective, $context,
    			$actors, $resources, $exception, $episodes);
    
    	$result = checkScenarioExists($id_newProject, $title);
    
    	$this->assertTrue($result);
    	$this->assertNotNull($id_incluido);
    
    	removeProject($id_newProject);
    }
    
    
}

?>
