<?php

class ProjetoController extends Zend_Controller_Action
{

    public function init()
    {
        $this->project = new Model_DbTable_Proj();
    }

    public function indexAction()
    {
        
         $select = $this->project->select();
         $select->where('nome = ?', 'Proj A');
         $this->view->proj = $this->project->fetchAll($select);
    }



}

