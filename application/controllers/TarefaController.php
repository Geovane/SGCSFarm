<?php
    
class TarefaController extends Zend_Controller_Action
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
        
        $this->funFazTarefa = new Application_Model_DbTable_FunFazTarefa();
        $this->tarefa = new Application_Model_DbTable_Tarefa();
        $this->estado = new Application_Model_DbTable_Estado();
        $this->projeto = new Model_DbTable_Proj();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->funcionario = new Model_DbTable_Func();
        $this->tarColabProj = new Model_DbTable_TarColabProj();
        
        
    }

    public function indexAction()
    {
        // action body
        
        $select = $this->projeto->select();
        $this->view->projeto = $this->projeto->fetchAll($select);
        
    }

    public function createAction()
    {
        // action body
        $idProj = $this->_getParam('idProj');
        $result  = $this->projeto->find($idProj);
        $this->view->projetoEncontrado = $result->current();
        
        
        $idColab = $this->_getParam('idColab');
        $this->view->idColab = $idColab;
        
        $where = $this->colaboradores->getAdapter()->quoteInto('idcolaboradores = ?', $idColab);
        $select = $this->colaboradores->select()
                ->where($where);
        $colabEncontrado = $this->colaboradores->fetchRow($select);
        $fk_funcionario = $colabEncontrado->funcionario_idfuncionario;
        
        $result  = $this->funcionario->find($fk_funcionario);
        $this->view->funcionario = $result->current();
        
        $this->view->estadoTarefa = $this->estado->fetchAll();

        if( $this->getRequest()->isPost() ) {
            $dataInc = $this->inverte_data($this->_request->getPost('dataInc'), "/");
            $dataInc = $dataInc." ".date("H:i:s");

            $dataFim = $this->inverte_data($this->_request->getPost('dataFim'), "/");
            $dataFim = $dataFim." ".date("H:i:s");

//            $dataEntrega = $this->inverte_data($this->_request->getPost('dataEntrega'), "/");
//            $dataEntrega = $dataEntrega." ".date("H:i:s");

            $dados = array(
                'descricao'  => $this->_request->getPost('descricao'),
                'dataInc'  => $dataInc,
                'dataFim' => $dataFim,
                'estado_idestado' => '2',//Tarefa sempre inicia em "estado inicial"
                'dataEntrega'  => ''
            );
            
            $idInseridoTarefa = $this->tarefa->insert($dados);
            
            $dadosFunFazTar = array(
                'tarefa_idtarefa'  => $idInseridoTarefa,
                'colaboradores_idcolaboradores'  => $idColab
            );
            
            $idInseridoFunFazTar = $this->funFazTarefa->insert($dadosFunFazTar);
            
            if(($idInseridoFunFazTar == null) || ($idInseridoTarefa == null)){
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
            }else{
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/1');
            }
        }
        
    }

    public function editAction()
    {
        // action body
        $idProj = $this->_getParam('idProj');
        $result  = $this->projeto->find($idProj);
        $this->view->projetoEncontrado = $result->current();
        
        
        $idColab = $this->_getParam('idColab');
        $this->view->idColab = $idColab;
        
        $where = $this->colaboradores->getAdapter()->quoteInto('idcolaboradores = ?', $idColab);
        $select = $this->colaboradores->select()
                ->where($where);
        $colabEncontrado = $this->colaboradores->fetchRow($select);
        $fk_funcionario = $colabEncontrado->funcionario_idfuncionario;
        
        $result  = $this->funcionario->find($fk_funcionario);
        $this->view->funcionario = $result->current();
        
        
        $idTarefa = $this->_getParam('idTarefa');
        $result  = $this->tarefa->find($idTarefa);
        $this->view->id = $idTarefa;
        $this->view->tarefaEncontrada = $result->current();
        $this->view->estadoTarefa = $this->estado->fetchAll();
        
        if( $this->getRequest()->isPost() ) {
            $dataInc = $this->inverte_data($this->_request->getPost('dataInc'), "/");
            $dataInc = $dataInc." ".date("H:i:s");

            $dataFim = $this->inverte_data($this->_request->getPost('dataFim'), "/");
            $dataFim = $dataFim." ".date("H:i:s");

            $dataEntrega = $this->inverte_data($this->_request->getPost('dataEntrega'), "/");
            $dataEntrega = $dataEntrega." ".date("H:i:s");

            $dados = array(
                'descricao'  => $this->_request->getPost('descricao'),
                'dataInc'  => $dataInc,
                'dataFim' => $dataFim,
                'estado_idestado' => $this->_request->getPost('estadoTarefa'),
                'dataEntrega'  => $dataEntrega
            );
            
            $where = $this->tarefa->getAdapter()->quoteInto("idtarefa = ?", $idTarefa);
            $idAtualizado = $this->tarefa->update($dados, $where);
            if($idAtualizado == null){
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
            }else{
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/2');
            }
        }
    }

    public function deleteAction()
    {
        // action body
        $idTarefa = $this->_getParam('idTarefa');
        $idColab = $this->_getParam('idColab');
        
        $where = $this->funFazTarefa->getAdapter()->quoteInto('tarefa_idtarefa = ?', $idTarefa, 'colaboradores_idcolaboradores = ?', $idColab);
        $idDelatadoFunfazTar = $this->funFazTarefa->delete($where);
        
        $where = $this->tarefa->getAdapter()->quoteInto('idtarefa = ?', $idTarefa);
        $idDelatadoTar = $this->tarefa->delete($where);
        
        $idProj = $this->_getParam('idProj');
        if(($idDelatadoFunfazTar == null) || ($idDelatadoTar == null)){
            $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
        }else{
            $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/3');
        }
    }

    private function inverte_data($data, $separador)
    {
        $nova_data = implode("".$separador."",array_reverse(explode("".$separador."",$data)));
        return $nova_data;
    }

    public function preparaAction()
    {
        // action body
        $this->view->flag = $this->_request->getParam('flag');
        $listProj = $this->_getParam('idProj');
        if( ($this->getRequest()->isPost()) || ($listProj != null) ){
            if($listProj == null)
                $listProj = $this->_request->getPost('listProj');
            
            $idColab = $this->_request->getPost('listColab');
            $idProj = $this->_request->getPost('idProj');
            
            if(($idColab != null) && ($idProj != null)){
                $this->_redirect('/tarefa/create/idProj/'.$idProj.'/idColab/'.$idColab.'');
            }
            if($listProj == null){
                $this->_redirect('/tarefa');
            }else{
                $result  = $this->projeto->find($listProj);
                $this->view->projEncontrado = $result->current();                
                
                $where = $this->colaboradores->getAdapter()->quoteInto('projeto_idprojeto = ?', $listProj);
                $select = $this->colaboradores->select()
                        ->where($where);
                $this->view->listaColaboradores = $this->colaboradores->fetchAll($select);
                
                $select = $this->funcionario->select();
                $this->view->listaFuncionarios = $this->funcionario->fetchAll($select);
                
                $select = $this->estado->select();
                $this->view->listaEstado = $this->estado->fetchAll($select);
//                $select = $this->funFazTarefa->select();
//                $this->view->tabFunFazTarefa = $this->funFazTarefa->fetchAll($select);
                
                
                $where = $this->projeto->getAdapter()->quoteInto('idprojeto = ?', $listProj);
                $select = $this->projeto->select()
                        ->where($where);
                $projEncontrado = $this->projeto->fetchRow($select);
                $projEncontrado = $projEncontrado->nome;
                
                $where = $this->projeto->getAdapter()->quoteInto('nomeProj = ?', $projEncontrado);
                $select = $this->tarColabProj->select()
                        ->where($where);
                $this->view->listaTarColabProj = $this->tarColabProj->fetchAll($select);
               
            }
            
        }
    }

}
