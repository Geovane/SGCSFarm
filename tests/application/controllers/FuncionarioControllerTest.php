<?php

require_once 'PHPUnit/Framework/TestCase.php';

class FuncionarioControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        // Assign and instantiate in one step:
        $this->bootstrap = new Zend_Application(
            'testing',
            APPLICATION_PATH . '/configs/application.ini'
        );
        parent::setUp();

    }

    public function tearDown()
    {
        /* Tear Down Routine */
    }

    public function testHomePage()
    {
        $this->dispatch('/');
        // ...
    }


}

