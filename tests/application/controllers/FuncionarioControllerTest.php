<?php

class FuncionarioControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
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
    
  /* public function init()
    {
   
        $this->funcionario = new Model_DbTable_Func();
        $this->filial = new Model_DbTable_Filial();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->projeto = new Model_DbTable_Proj();
        $this->userGit = new Model_DbTable_UserGit();
        $this->userBug = new Model_DbTable_UserBugzilla();
        $this->redefinir = new Model_DbTable_RedefinirSenha();
        
}*/
   /* public function testecreateaction($nome, $documentoIdentificacao, $login, $senha, $email, $empresaFilial_idempresaFilial){
                
            $data = array(
                    'nome'  => $nome,
                    'documentoIdentificacao'  => $documentoIdentificacao,
                    'login' => $login,
                    'senha'  => sha1($senha),
                    'email'  => $email,
                    'empresaFilial_idempresaFilial' => $empresaFilial_idempresaFilial);
            
            $idInserido = $this->funcionario->insert($data); 
            
            $select = $this->funcionario->select();
            $select->from($this->funcionario);
            $select->where('funcionario_idfuncionario = ?', $idInserido); */
    
        /*public function testeCreateAction(){            
                $foo = new FuncionarioController();
                $this->request->setMethod('POST')
                ->setPost(array(
                    'nome'  => '$nome',
                    'documentoIdentificacao'  => '$documentoIdentificacao',
                    'login' => '$login',
                    'senha'  => '$senha',
                    'email'  => '$email',
                    'empresaFilial_idempresaFilial' => '$empresaFilial_idempresaFilial'));
                createAction();           
    }
        
              
        
        public function testeCreateAction(){            
               
                $this->request->setMethod('POST')
                ->setPost(array(
                    'nome'  => '$nome',
                    'documentoIdentificacao'  => '4123245',
                    'login' => '$login',
                    'senha'  => '$senha',
                    'email'  => '$email',
                    'empresaFilial_idempresaFilial' => '$empresaFilial_idempresaFilial'));
                $this->dispatch('/funcionario/create');
                $this->assertController('funcionario');
                $this->assertAction('create');
    }       
    
    public function testeCreateAction1(){
    
        return 1;
        }      
    
    public function tearDown(){
        Tear Down Routine
         * 
         * } */
    
     public function testAction()
    {
        $this->dispatch('/funcionario/create');
        $this->assertController('funcionario');
        $this->assertAction('create');
    }
    
    
    
    
    
}

//$foo = new FuncionarioControllerTest;
  //   testeCreatAction();