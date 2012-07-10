<?php

class Model_DbTable_EstadoProj extends Zend_Db_Table_Abstract
{
    protected $_name = 'estado_projeto';
    protected $_primary = 'nomeProj';
    
    //busca o estado de um projeto pelo id.
     public function buscaEstadoProj($idProj)
    {
        $select = $this->select()
               ->where('idprojeto = ?', $idProj);

        return ($this->fetchRow($select)->tipoDeEstado);
    }
    
}

