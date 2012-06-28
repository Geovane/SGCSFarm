<?php

class Model_DbTable_UserBugzilla extends Zend_Db_Table_Abstract
{

    protected $_name = 'usuarioBugzilla';
    protected $_primary = 'funcionario_idfuncionario';

    //Retorna true se existir um usuario BugZilla relacionado ao funcionario com id = $func_id
     public function existeUserBug($func_id)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('funcionario_idfuncionario = ?', $func_id);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }
	

}

