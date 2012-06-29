<?php

class Model_DbTable_UserGit extends Zend_Db_Table_Abstract
{

    protected $_name = 'usuarioGit';
    protected $_primary = 'funcionario_idfuncionario';

    //Retorna true se existir um usuario git relacionado ao funcionario com id = $func_id
     public function existeUserGit($func_id)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('funcionario_idfuncionario = ?', $func_id);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }
	

}

