<?php

class ProjetoController extends Zend_Controller_Action
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

        $this->project = new Model_DbTable_Proj();
        $this->funcionario = new Model_DbTable_Func();
        $this->estado = new Model_DbTable_Estado();
        $this->projbugzilla = new Model_DbTable_ProjBugzilla();
        $this->projgit = new Model_DbTable_ProjGit();
    }

    public function indexAction()
    {
        
         $select = $this->project->select()->order('nome');
         
         $rows = $this->project->fetchAll($select);
         
         $paginator = Zend_Paginator::factory($rows);
         $paginator->setItemCountPerPage(5);
         
         $this->view->paginator = $paginator;
         $paginator->setCurrentPageNumber($this->_getParam('page'));
         
    }
    
    public function createAction()
    {
        $this->view->funcionario = $this->funcionario->fetchAll();
        $this->view->estado = $this->estado->fetchAll();
        
        if($this->_request->isPost())    
        {
            $data = array
            (
              'nome' => $this->_request->getPost('nome'),
              'descricao' => $this->_request->getPost('descricao'),
              'dataInc' => $this->_request->getPost('dtinicio'),
              'dataFim' => $this->_request->getPost('dtfim'),
              'idGerente' => $this->_request->getPost('idGerente'),
              'estado_idestado' => $this->_request->getPost('estado')  
            );
            
            $idprojetoinserido = $this->project->insert($data);
            
            $data1 = array
            (
              'projeto_idprojeto' => $idprojetoinserido,
              'nomeProjeto' => $this->_request->getPost('nome')  
            );
            
            $this->projbugzilla->insert($data1);
            
            $data2 = array
            (
                'projeto_idprojeto' => $idprojetoinserido,
                'repositorio' => $this->_request->getPost('nome'),
                'chave' => "asdasdsda"
            );
            
            $this->projgit->insert($data2);
            
            $this->_redirect('projeto/index');
        }
    }



}

