<?php

//require_once dirname(__FILE__) . '/../../../application/controllers/FuncionarioController.php';

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
public function existeLogin($login)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('login = ?', $login);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }

    
    public function existeDoc($doc)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('documentoIdentificacao = ?', $doc);

        return ($this->fetchRow($select)->num == 0) ? false : true;
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
                        'nome'  => 'manolo das dorgas',
                        'documentoIdentificacao'  => '123321',
                        'login' => 'manolo',
                        //'senha'  => '123321',      //sha1('123'),
                        'email'  => 'manolo@dorgas.com',
                        //'empresaFilial_idempresaFilial' => '2',
                        //'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
                    ));
        //cria o funcionario e verifica se foi criado
        $this->dispatch('/funcionario/create');
        $this->assertRedirectTo('/funcionario/index/flag/1');
        //busca o funcionario criado no banco
        $select = $this->funcionario->select();
        $select->from($this->funcionario, 'nome');
        $select->where('nome = ?', 'manolo das dorgas');
        //verifica se o funcionario foi encontrado e confirma se sim e fala se não
        if($this->funcionario->fetchRow($select)->idfuncionario == 'manolo das dorgas'){
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
      $this->dispatch('/auth/login');  
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
    	unset($_POST);
        //faz login
        $this->request->setMethod('POST')
              ->setPost(array(
                  'login' => 'mimoso',
                  'senha' => '123'
              ));
              unset($_POST);
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
      unset($_POST);
      
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



    public function init()
    {
        //Verifica se o usuario esta autenticado, caso não esteja ele é redirecionado para a tela da login
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }

        //Pega as informações do usuario logado no sistema.
        $this->funcLogado = Zend_Auth::getInstance()->getIdentity();
        //Envia pra view
        $this->view->funcLogado = $this->funcLogado;

        //Informações de exibição do usuario no index (deve estar em todos os inits)
        $this->FuncFilial = new Model_DbTable_FuncFilial();
        $dadosIndex = $this->FuncFilial->find($this->funcLogado->idfuncionario);
        $this->view->dadosIndex = $dadosIndex[0];

        //Dados do usuario logado para serem utilizados nas actions
        $this->idFunc = $this->funcLogado->idfuncionario;
        $this->idEmpresa = $dadosIndex[0]->empresa_idempresa;
        $this->idFilial = $this->funcLogado->empresaFilial_idempresaFilial;

        $idFunc = $this->idFunc;
        $idFilial = $this->idFilial;
        $idEmpresa =  $this->idEmpresa;

        //Informações relativas a permissoes (Se tiver permissão retorna True)
        $this->adminFilial = Model_Permissoes::responsavelFilial($idFunc,$idFilial);
        $this->adminEmpresa = Model_Permissoes::responsavelEmpresa($idFunc,$idEmpresa);

        $this->view->AdminFilial = $this->adminFilial;
        $this->view->AdminEmpresa = $this->adminEmpresa;

      
        /* Initialize action controller here */
        $this->funcionario = new Model_DbTable_Func();
        $this->filial = new Model_DbTable_Filial();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->projeto = new Model_DbTable_Proj();
        $this->userGit = new Model_DbTable_UserGit();
        $this->userBug = new Model_DbTable_UserBugzilla();
        $this->redefinir = new Model_DbTable_RedefinirSenha();

    }

    public function indexAction()
    {
        //Verifica permissão
        if(!$this->adminFilial){
            $this->_redirect('/index/negado');
        }
        
         $this->view->flag = $this->_request->getParam('flag');
         $select = $this->funcionario->select();
         $select->where('empresaFilial_idempresaFilial = ?', $this->idFilial);
         $select->order('nome');
         $rows = $this->funcionario->fetchAll($select);

         //Cria a paginação relativa a exibição dos funcionarios

         $paginator = Zend_Paginator::factory($rows);
         //Passa o numero de registros por pagina
         $paginator->setItemCountPerPage(10);
      
         $this->view->paginator = $paginator;
         $paginator->setCurrentPageNumber($this->_getParam('page'));

    }


    public function createAction()
    {

        //Verifica permissão
        if(!$this->adminFilial){
            $this->_redirect('/index/negado');
        }

        $this->view->flag = $this->_request->getParam('flag');
        
         if ( $this->_request->isPost() )
            {

             if(!$this->funcionario->existeDoc($this->_request->getPost('doc')))
             {
                 if(!$this->funcionario->existeLogin($this->_request->getPost('login')))
                 {
                    $data = array(
                        'nome'  => $this->_request->getPost('nome'),
                        'documentoIdentificacao'  => $this->_request->getPost('doc'),
                        'login' => $this->_request->getPost('login'),
                        'senha'  => sha1($this->_request->getPost('doc')),
                        'email'  => $this->_request->getPost('email'),
                        'empresaFilial_idempresaFilial' => $this->idFilial,
                        'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
                    );

                    //print_r($data);

                    //Insere funcionario e guardo o id dele na variavel $idInserido
                    $idInserido = $this->funcionario->insert($data);

                    //Cria usuario Git e bugZilla
                    $data1 = array(
                        'funcionario_idfuncionario'  => $idInserido,
                        'usuario'  => $this->_request->getPost('login'),
                        'senha'  => sha1($this->_request->getPost('doc'))
                    );

                     //print_r($data1);
                   //Insere usuario Git e bugZilla
                   $this->userBug->insert($data1);
                   $this->userGit->insert($data1);

                   $this->_redirect('funcionario/index/flag/1');

                 }else{
                   $this->_redirect('funcionario/create/flag/2');
                 }
             }else{

                  $this->_redirect('funcionario/create/flag/1');
             }

            }
    }

    public function editAction(){

        //Verifica permissão
        if(!$this->adminFilial){
            $this->_redirect('/index/negado');
        }

       $func_id = $this->_getParam('id');

       $result  = $this->funcionario->find($func_id);
       $this->view->funcionario = $result->current();

            if ( $this->_request->isPost() )
            {

                $data = array(
                    'nome'  => $this->_request->getPost('nome'),
                    'email'  => $this->_request->getPost('email'),
                );

                $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('id'));

                $this->funcionario->update($data, $where);

                $this->_redirect('/funcionario/index/flag/2');
            }


    }

    public function deleteAction(){

         //Verifica permissão
        if(!($this->adminFilial || $this->adminEmpresa) ){
            $this->_redirect('/index/negado');
        }

        $func_id = $this->_getParam('id');
        $tipo = $this->_getParam('tipo');

            //Verifica se o funcionario esta inserido como colaborador em algum projeto
            $select = $this->colaboradores->select();
            $select->from($this->colaboradores, 'COUNT(*) AS num');
            $select->where('funcionario_idfuncionario = ?', $func_id);

            //Verifica se o funcionario é gerente de algum projeto
            $selectP = $this->projeto->select();
            $selectP->from($this->projeto, 'COUNT(*) AS num');
            $selectP->where('idGerente = ?', $func_id);

            //Verifica se o funcionario é responsavel por alguma filial
            $selectF = $this->filial->select();
            $selectF->from($this->filial, 'COUNT(*) AS num');
            $selectF->where('responsavel = ?', $func_id);



            if ( $this->colaboradores->fetchRow($select)->num == 0 && $this->projeto->fetchRow($selectP)->num == 0 && $this->filial->fetchRow($selectF)->num == 0 )
            {
                //Chama metodo que deleta os registros de outras tabelas referentes ao funcionario
                $this->deletarRegistrosUsuario($func_id);

                //deleta o funcionario
                $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', $func_id);
                $this->funcionario->delete($where);

                if($tipo!=2)
                $this->_redirect('funcionario/index/flag/3');

                $this->_redirect('funcionario/indexemp/flag/3');

            } else
            {
              if($tipo!=2)
              $this->_redirect('funcionario/index/flag/4');

              $this->_redirect('funcionario/indexemp/flag/4');
            }

    }

     /*
     * Método que deleta do banco todos os registros que envolvem o usuário
     */
    private function deletarRegistrosUsuario($func_id)
    {
        if($this->redefinir->existeRedefinir($func_id)){
        //deleta todas as solicitaçãoes de redefinição de senha
        $where = $this->redefinir->getAdapter()->quoteInto('funcionario_idfuncionario = ?', $func_id);
        $this->redefinir->delete($where);
        }

        if($this->userGit->existeUserGit($func_id)){
        //deleta o usuario git relacionado a esse funcionario
        $where = $this->userGit->getAdapter()->quoteInto('funcionario_idfuncionario = ?', $func_id);
        $this->userGit->delete($where);
        }

        if($this->userBug->existeUserBug($func_id)){
        //deleta o usuario bugZilla relacionado a esse funcionario
        $where = $this->userBug->getAdapter()->quoteInto('funcionario_idfuncionario = ?', $func_id);
        $this->userBug->delete($where);
        }

        return true;
    }


        public function indexempAction()
        {

        //Verifica permissão
        if(!$this->adminEmpresa){
            $this->_redirect('/index/negado');
        }

         $this->view->flag = $this->_request->getParam('flag');
         $select = $this->funcionario->select()->order('nome');

         $rows = $this->funcionario->fetchAll($select);

         //Cria a paginação relativa a exibição dos funcionarios

         $paginator = Zend_Paginator::factory($rows);
         //Passa o numero de registros por pagina
         $paginator->setItemCountPerPage(10);

         $this->view->paginator = $paginator;
         $paginator->setCurrentPageNumber($this->_getParam('page'));

    }


    public function createempAction()
    {
        //Verifica permissão
        if(!$this->adminEmpresa){
            $this->_redirect('/index/negado');
        }

        $this->view->flag = $this->_request->getParam('flag');

         if ( $this->_request->isPost() )
            {

             if(!$this->funcionario->existeDoc($this->_request->getPost('doc')))
             {
                 if(!$this->funcionario->existeLogin($this->_request->getPost('login')))
                 {
                    $data = array(
                        'nome'  => $this->_request->getPost('nome'),
                        'documentoIdentificacao'  => $this->_request->getPost('doc'),
                        'login' => $this->_request->getPost('login'),
                        'senha'  => sha1($this->_request->getPost('doc')),
                        'email'  => $this->_request->getPost('email'),
                        'empresaFilial_idempresaFilial' => (int)0,
                        'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
                    );

                    //print_r($data);

                    //Insere funcionario e guardo o id dele na variavel $idInserido
                    $idInserido = $this->funcionario->insert($data);

                    //Cria usuario Git e bugZilla
                    $data1 = array(
                        'funcionario_idfuncionario'  => $idInserido,
                        'usuario'  => $this->_request->getPost('login'),
                        'senha'  => sha1($this->_request->getPost('doc'))
                    );

                     //print_r($data1);
                   //Insere usuario Git e bugZilla
                   $this->userBug->insert($data1);
                   $this->userGit->insert($data1);

                   $this->_redirect('funcionario/indexemp/flag/1');

                 }else{
                   $this->_redirect('funcionario/createemp/flag/2');
                 }
             }else{

                  $this->_redirect('funcionario/createemp/flag/1');
             }

            }
    }

    public function editempAction(){

        //Verifica permissão
        if(!$this->adminEmpresa){
            $this->_redirect('/index/negado');
        }

       $func_id = $this->_getParam('id');

       $result  = $this->funcionario->find($func_id);
       $this->view->funcionario = $result->current();
       $this->view->filial = $this->filial->fetchAll();

            if ( $this->_request->isPost() )
            {
            //Guarda o id do fuincionario e da filial dele, enviados via post
            $id = $this->_request->getPost('id');
            $idfilial = $this->_request->getPost('id_filial');

            if($idfilial == $this->_request->getPost('idFilial') )
            {
                    $data = array(
                            'nome'  => $this->_request->getPost('nome'),
                            'email'  => $this->_request->getPost('email')
                        );

                    $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('id'));
                    $this->funcionario->update($data, $where);
                    $this->_redirect('funcionario/indexemp/flag/2');


            }
            else{

                    //Verifica se o funcionario esta inserido como colaborador em algum projeto
                    $select = $this->colaboradores->select();
                    $select->from($this->colaboradores, 'COUNT(*) AS num');
                    $select->where('funcionario_idfuncionario = ?', $id);

                    //Verifica se o funcionario é gerente de algum projeto
                    $selectP = $this->projeto->select();
                    $selectP->from($this->projeto, 'COUNT(*) AS num');
                    $selectP->where('idGerente = ?', $id);

                    //Verifica se o funcionario é responsavel por alguma filial
                    $selectF = $this->filial->select();
                    $selectF->from($this->filial, 'COUNT(*) AS num');
                    $selectF->where('responsavel = ?', $func_id);


                    if ( $this->colaboradores->fetchRow($select)->num == 0 && $this->projeto->fetchRow($selectP)->num == 0 && $this->filial->fetchRow($selectF)->num == 0)
                    {
                        $data = array(
                            'nome'  => $this->_request->getPost('nome'),
                            'email'  => $this->_request->getPost('email'),
                            'empresaFilial_idempresaFilial' => $this->_request->getPost('idFilial')
                        );

                        $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('id'));
                        $this->funcionario->update($data, $where);
                        $this->_redirect('funcionario/indexemp/flag/5');
                    }
                    else{

                        $this->_redirect('funcionario/indexemp/flag/4');
                    }
          }

        }
    }
}