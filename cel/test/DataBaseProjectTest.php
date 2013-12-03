<?php

include ("..cel/aplicacao/dataBaseProject/dataBase/DatabaseProject.php");

class DataBaseProjectTest extends PHPUnit_Framework_TestCase
{
    
    private function testBase()
    {
        $_SESSION['id_usuario_corrente'] = "Teste";
        ob_start();
        require_once ("../aplicacao/dataBase/DatabaseProject.php");
        return ob_get_clean();
    }
    
    /*
     @test
    */
    public function testIncludeProject() 
    {  
        $this->testBase();
        
        $id_newProject = includeProject("Projeto Teste", "teste");
        
        $this->assertNotNull($id_newProject);
        
        removeProject($id_newProject);
    }
    
    /*
     @test
    */
    public function testRemoveProject() {
    
    	$this->testBase();
    	$name = "Projeto Teste";
    	$id_newProject = includeProject($name, "teste");
    
    	removeProject($id_newProject);
    
    	$queryVerification = "SELECT * FROM projeto WHERE nome = '$name'";
    	$queryVerificationResult = mysql_query($queryVerification);
    	$resultArray = mysql_fetch_array($queryVerificationResult);
    	$this->assertEquals(false,$resultArray);
    }
   

}

?>
