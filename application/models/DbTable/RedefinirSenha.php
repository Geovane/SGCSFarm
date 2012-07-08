<?php

class Model_DbTable_RedefinirSenha extends Zend_Db_Table_Abstract
{

    protected $_name = 'redefinirSenha';
    protected $_primary = 'hash';

    //Retorna true se existir alguma solicitação de redefinição de senha para o funcionario com id = $func_id
    public function existeRedefinir($func_id)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('funcionario_idfuncionario = ?', $func_id);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }
    
}

