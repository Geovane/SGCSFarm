<?php

require_once dirname(__FILE__) . '/../../../application/controllers/ColaboradorController.php';

/**
 * Test class for ColaboradorController.
 * Generated by PHPUnit on 2012-07-10 at 02:21:57.
 */
class ColaboradorControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

    /**
     * @var ColaboradorController
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        // Assign and instantiate in one step:
        $this->bootstrap = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH
           . '/configs/application.ini'
        );
        parent::setUp();
        Zend_Controller_Front::getInstance()->setParam('noErrorHandler',true)->throwExceptions(true); 
    
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @todo Implement testInit().
     */
    public function testInit() {
        $this->dispatch('/Colaborador/init');
        $this->assertController('Colaborador');
        $this->assertAction('init');
    }

    /**
     * @todo Implement testIndexAction().
     */
    public function testIndexAction() {
        $this->dispatch('/Colaborador/index');
        $this->assertController('Colaborador');
        $this->assertAction('index');
    }

    /**
     * @todo Implement testCreateAction().
     */
    public function testCreateAction() {
        // Remove the following lines when you implement this test.
        $this->request->setMethod('POST')
              ->setPost(array(
                'id' => '4',
                'funcionario' => '23',
                'horas'  => '10',
                'funcao' => '10'
              ));
        $this->dispatch('/colaborador/create');
        $this->AssertRedirectTo('/colaborador/index/id/4/flag/2');
        
        $this->dispatch('/colaborador/delete/idfunc/23/id/4');
    }

    /**
     * @todo Implement testEditAction().
     */
    public function testEditAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDeleteAction().
     */
    public function testDeleteAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
