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
        $this->funcaoproj = new Model_DbTable_FuncaoProjeto();
        $this->colab = new Model_DbTable_Colaboradores();
        
    }

    public function indexAction()
    {
        
         $select = $this->project->select();
         $select ->order('nome');
         
         $rows = $this->project->fetchAll($select);
         
         $paginator = Zend_Paginator::factory($rows);
         $paginator->setItemCountPerPage(5);
         
         $this->view->paginator = $paginator;
         
         $paginator->setCurrentPageNumber($this->_getParam('page'));
         
    }
    
    public function createAction()
    {
        $this->view->funcionario = $this->funcionario->fetchAll();
        
        
        if($this->_request->isPost())    
        {
            $data = array
            (
              'nome' => $this->_request->getPost('nome'),
              'descricao' => $this->_request->getPost('descricao'),
              'dataInc' => $this->_request->getPost('dtinicio'),
              'dataFim' => "",
              'idGerente' => $this->_request->getPost('idGerente'),
              'estado_idestado' => "2"  
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
    
    public function editAction()
    {
        $id_proj = $this->_getParam('id');
        
        $result = $this->project->find($id_proj);
        $this->view->projeto = $result->current();
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
            
            $where = $this->project->getAdapter()->quoteInto('idprojeto = ?', (int) $this->_request->getPost('id'));
            
            $this->project->update($data,$where);
            
            $this->_redirect('projeto/index');
        }
    }

    public function deleteAction()
    {
        $id_proj = $this->_getParam('id');
        
        $select = $this->project->select();
        $select -> from($this->project, 'COUNT(*) AS num')
                -> where('idprojeto = ?', $id_proj)
                ->where('estado_idestado = 7');
        
        if($this->project->fetchRow($select)->num != 0)
        {
            $where = $this->projbugzilla->getAdapter()->quoteInto('projeto_idprojeto = ?',$id_proj);
            $this->projbugzilla->delete($where);
            
            $where = $this->projgit->getAdapter()->quoteInto('projeto_idprojeto = ?',$id_proj);
            $this->projgit->delete($where);
            
            $where = $this->project->getAdapter()->quoteInto('idprojeto = ?',$id_proj);
            $this->project->delete($where);
            
            $this->_redirect('projeto/index');
        }    
    }
    
    public function colabAction()
    {
        $id_proj = $this->_getParam('id');
        
        $result = $this->project->find($id_proj);
        
        $idgerente= $this->project->select();
        $idgerente -> from($this->project,array('idGerente'))
                   -> where('idprojeto = ?',$id_proj);
        
        $func = $this->funcionario->select();
        $func -> where('idfuncionario != ?',$idgerente);
        
        $funcao = $this->funcaoproj->select();
        $funcao -> where('idfuncaoProjeto != 40');
        
        $this->view->projeto = $result->current();
        $this->view->funcionario = $this->funcionario->fetchAll($func);
        $this->view->funcao = $this->funcaoproj->fetchAll($funcao);
        
        if($this->_request->isPost())
        {
            $data = array
            (
              'projeto_idprojeto' => $this->_request->getPost('id'),
              'funcionario_idfuncionario' => $this->_request->getPost('funcionario'),
              'funcaoProjeto_idfuncaoProjeto' => $this->_request->getPost('funcao')
            );
            
            $this->colab->insert($data);
            
            $this->_redirect('projeto/index');
        }
    }        
}

