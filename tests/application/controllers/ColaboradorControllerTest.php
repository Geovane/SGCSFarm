<?php

require_once dirname(__FILE__) . '/../../../application/controllers/ColaboradorController.php';

/**
 * Classe de teste do ColaboradorController, essa classe possui métodos que realizam testes para validar
 * o funcionamento do ColaboradorController.
 * Generated by PHPUnit on 2012-07-10 at 02:21:57.
 * @author Luiz Daud
 * @version 0.1
 * @access public
 */

class ColaboradorControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

    /**
     * Funcao que inicializa todos os parametros necessarios para o correto
     * funcionamento dos testes, como conexões com o banco de dados e
     * variaveis de controle dos testes.
     *
     * @author Luiz Daud
     * @access public
     * @return void
     *
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
     * Função que verifica se o action init está sendo alcançado corretamente.
     *
     * @author Luiz Daud
     * @access public
     * @return void
     */
    
    public function testInit() {
       //faz login
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '1234'
              ));
        $this->dispatch('/Auth/login');
        //verifica os action
        $this->assertController('Auth');
        $this->assertAction('login');   
        //vai pro init       
        $this->dispatch('/Colaborador/init');
        //verifica os novos actions
        $this->assertController('Colaborador');
        $this->assertAction('init');
    }

     /**
     * Função que verifica se o action index está sendo alcançado corretamente.
     *
     * @author Luiz Daud
     * @access public
     * @return void
     */
    public function testIndexAction() {
        //faz login
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '1234'
              ));
        $this->dispatch('/Auth/login');
        //verifica os actions
        $this->assertController('Auth');
        $this->assertAction('login');          
        
        //vai no index
        $this->dispatch('/Colaborador/index');
        //verifica os actions
        $this->assertController('Colaborador');
        $this->assertAction('index');
    }

    /**
     * Função que verifica se o action create está sendo executado corretamente,
     * cria um colaborador num projeto e verifica se a operação foi concluida
     *
     * @author Luiz Daud
     * @access public
     * @return void
     */
    public function testCreateAction() {
        //faz login
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '1234'
              ));
        $this->dispatch('/Auth/login');
        //verifica os actions
        $this->assertController('Auth');
        $this->assertAction('login');   
        //seta os dados do post pra criar
        $this->request->setMethod('POST')
              ->setPost(array(
                'id' => '4',
                'funcionario' => '1',
                'horas'  => '10',
                'funcao' => '10'
              ));
        //cria
        $this->dispatch('/colaborador/create');
        //verifica se criou
        $this->AssertRedirectTo('/colaborador/index/id/4/flag/2');
        //deleta o criado
        $this->dispatch('/colaborador/delete/idfunc/1/id/4');
    }

    /**
     * Função que verifica se o action edit está sendo executado corretamente,
     * cria um colaborador num projeto, edita e verifica se a operação foi concluida
     * logo após deleta o colaborador.
     *
     * @author Luiz Daud
     * @access public
     * @return void
     */
    public function testEditAction() {
        // faz login
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '1234'
              ));
        $this->dispatch('/Auth/login');
        //verifica os controllers
        $this->assertController('Auth');
        $this->assertAction('login');   
        //seta o post
        $this->request->setMethod('POST')
              ->setPost(array(
                'id' => '4',
                'funcionario' => '1',
                'horas'  => '10',
                'funcao' => '10'
              ));
        //cria o colaborador
        $this->dispatch('/colaborador/create');
        //verifica se foi criado
        $this->AssertRedirectTo('/colaborador/index/id/4/flag/2');
       //cria o post de edição
        $this->request->setMethod('POST')
              ->setPost(array(
                'idProj' => '4',
                'idFunc' => '1',
                'horas'  => '10',
                'funcao' => '10'
              ));
        //edita
        $this->dispatch('/colaborador/edit/idfunc/1/id/4');
        //verifica se foi editado
        $this->AssertRedirectTo('/colaborador/index/id/4/flag/1');
        //deleta o colaborador
        $this->dispatch('/colaborador/delete/idfunc/1/id/4');
        
    }

     /**
     * Função que verifica se o action delete está sendo executado corretamente,
     * cria um colaborador num projeto, deleta e verifica se a operação foi concluida.
     *
     * @author Luiz Daud
     * @access public
     * @return void
     */
    public function testDeleteAction() {
        //faz login 
        
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '1234'
              ));
        $this->dispatch('/Auth/login');
        //checa os controllers
        $this->assertController('Auth');
        $this->assertAction('login');   
        //seta o post
        $this->request->setMethod('POST')
              ->setPost(array(
                'id' => '4',
                'funcionario' => '1',
                'horas'  => '10',
                'funcao' => '10'
              ));
        //cria o colaborador
        $this->dispatch('/colaborador/create');
        //verifica se foi criado
        $this->AssertRedirectTo('/colaborador/index/id/4/flag/2');  
        //deleta o colaborador        
        $this->dispatch('/colaborador/delete/idfunc/1/id/4');       
    }

}

?>
