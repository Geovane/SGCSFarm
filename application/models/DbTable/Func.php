<?php

class Model_DbTable_Func extends Zend_Db_Table_Abstract
{

    protected $_name = 'funcionario';
    protected $_primary = 'idfuncionario';

    //Retorna true se já existir o login cadastrado
    public function existeLogin($login)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('login = ?', $login);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }

    //Retorna true se já existir o documento de identificação cadastrado
    public function existeDoc($doc)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('documentoIdentificacao = ?', $doc);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }
	

}

