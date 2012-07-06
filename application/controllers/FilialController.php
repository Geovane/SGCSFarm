<?php

class FilialController extends Zend_Controller_Action
{

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
        
        $this->funcionario = new Model_DbTable_Func();
        $this->filial = new Model_DbTable_Filial();
        $this->colaboradores = new Model_DbTable_Colaboradores();
    }

  /** 
     * Função inicial do controller tarefas.
     * envia ao seu view as informações sobre os projetos.
     * 
     * @access public 
     * @return void
     */
    
       public function indexAction()
    {
         $this->view->flag = $this->_request->getParam('flag');
         $select = $this->filial->select();
         $this->view->filial = $this->filial->fetchAll($select);
    }

    /** 
     * Função responsável pela inserção ao banco dos dados de uma nova tarefa
     * criada vinculada a um colaborador em um projeto.
     * 
     * @access public 
     * @return void
     */
    
     public function createAction()
    {

        $this->view->flag = $this->_request->getParam('flag');
        $func = $this->funcionario->select()
                ->where('empresaFilial_idempresaFilial = ?', 0);
        $this->view->funcionario = $this->funcionario->fetchAll( $func );
        //$this->view->empresa= $this->empresa->fetchAll();

        
            if ( $this->_request->isPost() )
            {

                $empresa = $this->filial->select()
                        ->where('empresa_idempresa = ?', $this->idEmpresa)
                        ->where('nome = ?', $this->_request->getPost('nome'));

                $numero = count($this->funcionario->fetchAll($empresa));

                if( $numero == 0){
                    
                    $data = array(
                        'nome'  => $this->_request->getPost('nome'),
                        'tel' => $this->_request->getPost('tel'),
                        'endereco'  => $this->_request->getPost('endereco'),
                        'responsavel'  => $this->_request->getPost('idResponsavel'),
                        'empresa_idempresa' => $this->idEmpresa,
                        'email'  => $this->_request->getPost('email'),
                        'cep' => $this->_request->getPost('cep')
                    );

                   //Insere empresa filial
                   $this->filial->insert($data);

                   $this->_redirect('/filial/index/flag/1');
                }else{

                   $this->_redirect('/filial/create/flag/1');
                    
                }
            }

    }


}





