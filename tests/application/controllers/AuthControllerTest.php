<?php


require_once dirname(__FILE__) . '/../../../application/controllers/AuthController.php';

/**
 * Test class for AuthController.
 * Generated by PHPUnit on 2012-07-09 at 22:48:03.
 */
class AuthControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
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
        $this->dispatch('/Auth');
        $this->assertFalse($this->response
                        ->isException());
        $this->assertNotRedirect();
    }
    
    public function testValidLoginShouldGoToProfilePage()
    {
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '123'
              ));
        $this->dispatch('/Auth/login');
        $this->assertRedirectTo('/index');
    }
 
    public function tearDown() {
        /* Tear Down Routine */
    }
}

?>