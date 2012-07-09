<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexControllerTest
 *
 * @author Luiz Daud
 */
class IndexControllerTestextends extends Zend_Test_PHPUnit_ControllerTestCase
{
    //put your code here
    public function setUp()
    {
          // Assign and instantiate in one step:
        $this->bootstrap = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH
           . '/configs/application.ini'
        );
        parent::setUp();
    
    }
    
    public function testHomePageIsASuccessfulRequest() {
        $this->dispatch('/index');
        $this->assertFalse($this->response
                        ->isException());
        $this->assertRedirectTo('auth/login');
    }
 
    public function tearDown() {
        /* Tear Down Routine */
    }
}

?>
