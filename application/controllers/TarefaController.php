<?php
    
class TarefaController extends Zend_Controller_Action
{

    public function init()
    {
        //Verifica se o usuario esta autenticado, caso não esteja ele é redirecionado para a tela da login
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }
        
        $this->tarefa = new Application_Model_DbTable_Tarefa();
        $this->estado = new Application_Model_DbTable_Estado();
    }

    public function indexAction()
    {
        // action body
                $select = $this->tarefa->select();
                $this->view->listaTarefa = $this->tarefa->fetchAll($select);
        
                $select = $this->estado->select();
                $this->view->estado = $this->tarefa->fetchAll($select);
    }

    public function createAction()
    {
        // action body
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
            
            $idInserido = $this->tarefa->insert($dados);
            $this->_redirect('/tarefa');
        }
    }

    public function editAction()
    {
        // action body
        $idTarefa = $this->_getParam('id');
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
            $this->tarefa->update($dados, $where);
            $this->_redirect('/tarefa');
        }
    }

    public function deleteAction()
    {
        // action body
        $idTarefa = $this->_getParam('id');
        $where = $this->tarefa->getAdapter()->quoteInto('idtarefa = ?', $idTarefa);
        $this->tarefa->delete($where);

        $this->_redirect('/tarefa');
        
    }

    private function inverte_data($data,$separador)
    {
        $nova_data = implode("".$separador."",array_reverse(explode("".$separador."",$data)));
        return $nova_data;
    }

}







