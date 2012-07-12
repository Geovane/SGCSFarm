<?php

require_once dirname(__FILE__) . '/../../../application/controllers/FuncionarioController.php';

/**
 * Test class for FuncionarioController.
 */
class FuncionarioControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
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
        
        //inicializa as tabelas do DB
        
        $this->funcionario = new Model_DbTable_Func();
        $this->filial = new Model_DbTable_Filial();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->projeto = new Model_DbTable_Proj();
        $this->userGit = new Model_DbTable_UserGit();
        $this->userBug = new Model_DbTable_UserBugzilla();
        $this->redefinir = new Model_DbTable_RedefinirSenha();
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
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testIndexAction().
     */
    public function testIndexAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCreateAction().
     */
    public function testCreateAction() {
        // Remove the following lines when you implement this test.
        
        //faz login
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '123'
              ));
        $this->dispatch('/Auth/login');
        //seta o post
        $this->request->setMethod('POST')
              ->setPost(array(
                        'nome'  => 'magnobaldo',
                        'documentoIdentificacao'  => '123321',
                        'login' => 'manolo',
                        'senha'  => sha1('123'),
                        'email'  => 'manolo@dorgas.com',
                        'empresaFilial_idempresaFilial' => '2',
                        'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
                    ));
        //cria o funcionario e verifica se foi criado
        $this->dispatch('/funcionario/create');
        $this->assertRedirectTo('/funcionario/index/flag/1');
        //busca o funcionario criado no banco
        $select = $this->funcionario->select();
        $select->from($this->funcionario, 'nome');
        $select->where('nome = ?', 'magnobaldo');
        //verifica se o funcionario foi encontrado e confirma se sim e fala se não
        if($this->funcionario->fetchRow($select)->idfuncionario == 'magnobaldo'){
          assert(1);
      }
      else
          assert(0);    
    }

    /**
     * @todo Implement testEditAction().
     */
    public function testEditAction() {
      //faz o login
      $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '123'
              ));
      $this->dispatch('/Auth/login');  
      //busca o funcionario
      $select = $this->funcionario->select();
      $select->from($this->funcionario, 'idfuncionario');
      $select->where('nome = ?', 'teste');
      //seta o post
      $this->request->setMethod('POST')
              ->setPost(array(
                  'nome'  => 'teste',
                  'email'  => 'teste@teste.com',
              ));
      //deflagra o edit e verifica se ocorreu
      $this->dispatch('/funcionario/edit/id23');
      $this->assertRedirectTo('/funcionario/index/flag/2');
    }

    /**
     * @todo Implement testDeleteAction().
     */
    public function testDeleteAction() {
      
        //faz login
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '123'
              ));
      //seta o post
       $this->request->setMethod('POST')
              ->setPost(array(
                        'nome'  => 'delete',
                        'documentoIdentificacao'  => 'delete',
                        'login' => 'delete',
                        'senha'  => sha1('delete'),
                        'email'  => 'delete@delete.com',
                        'empresaFilial_idempresaFilial' => '2',
                        'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
                    ));
      
      //cria um funcionario pra ser deletado 
      $this->dispatch('/funcionario/create');
      $this->assertRedirectTo('/funcionario/index/flag/1');
      
      //busca a id do funcinario criado
      
      $select = $this->funcionario->select();
      $select->from($this->funcionario, 'idfuncionario');
      $select->where('nome = ?', 'delete');
      //deleta o funcinario usando a id e verifica se foi deletado 
      $this->dispatch('/funcionario/delete/id/' + $this->funcionario->fetchRow($select)->idfuncionario );
      $this->assertRedirectTo('/funcionario/index/flag/3');     
    }

    
    /**
     * @todo Implement testIndexempAction().
     */
    public function testIndexempAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCreateempAction().
     */
    public function testCreateempAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testEditempAction().
     */
    public function testEditempAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
