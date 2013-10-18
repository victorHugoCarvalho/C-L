<?php

require_once ("../aplicacao/dataBase/DatabaseScenario.php");

class DatabaseScenarioTest extends PHPUnit_Framework_TestCase{
    
    
    public function testCheckScenarioExists()
    {
        
        $project = "1";
        $title = "Texto";
        $result = checkScenarioExists($project, $title);

        $this->assertEquals(false,$result);
    }
    
    
}

?>
