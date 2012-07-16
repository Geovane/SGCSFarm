<?php


require_once dirname(__FILE__) . '/../../../application/controllers/AuthController.php';

/**
 * Test class for AuthController.
 * Generated by PHPUnit on 2012-07-09 at 22:48:03.
 */
class AuthControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    //put your code here
    public function setUp(){
        // Assign and instantiate in one step:
        $this->bootstrap = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH
           . '/configs/application.ini'
        );
        parent::setUp();
       // Zend_Controller_Front::getInstance()->setParam('noErrorHandler',true)->throwExceptions(true); 
    
    }
    
    
    public function testValidLoginShouldGoToProfilePage()
    {
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '1234'
              ));
        $this->dispatch('/Auth/login');
        $this->assertController('Auth');
        $this->assertAction('login');
        $this->assertRedirectTo('/');

        
    }
    
    public function testInvalidCredentialsShouldResultInRedisplayOfLoginForm()
    {
        $request = $this->getRequest();
        $request->setMethod('POST')
                ->setPost(array(
                    'login' => 'bogus',
                    'senha' => 'reallyReallyBogus',
                ));
        $this->dispatch('/Auth');
        $this->assertController('Auth');
        $this->assertAction('index');
        $this->assertRedirectTo('/Auth/login');
        }
    
     public function testIndexActionShouldContainLoginForm()
    {
        $this->dispatch('/Auth/login');
        $this->assertAction('login');
        $this->assertQueryCount('form', 1);
    }
        
    public function testHomePageIsASuccessfulRequest() {
        $this->dispatch('/Auth');
        $this->assertController('Auth');
        $this->assertAction('index');
        $this->assertFalse($this->response
                        ->isException());
        $this->assertRedirectTo('/Auth/login');
    }
    
    public function testLogoutAction(){
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '1234'
              ));
        $this->dispatch('/Auth/login');
        $this->assertController('Auth');
        $this->assertAction('login');
        
        $this->dispatch('/auth/login');
        $this->assertRedirectTo('/Auth/login');
    }

public function testSenhaAction(){
    
    $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => '12345',
                  'senha' => '98765'
      ));    
    $this->dispatch('/Auth/login');
    $this->assertRedirectTo('/Auth/senha');
    }

    public function tearDown() {
        /* Tear Down Routine */
    }
}

?>