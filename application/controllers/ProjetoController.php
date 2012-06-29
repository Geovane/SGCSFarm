<?php

class ProjetoController extends Zend_Controller_Action
{

    public function init()
    {
        //Verifica se o usuario esta autenticado, caso não esteja ele é redirecionado para a tela da login
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }
        
        $this->project = new Model_DbTable_Proj();
    }

    public function indexAction()
    {
        
         $select = $this->project->select();
         $select->where('nome = ?', 'Proj A');
         $this->view->proj = $this->project->fetchAll($select);
    }



}

