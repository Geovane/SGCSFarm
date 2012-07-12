<?php
/**
 * Esta classe tem como objetivo efetuar a escolha da tabela 'usuarioGit'
 * no banco de dados.
 * 
 * @author SoftFarm
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 * 
 */
class Model_DbTable_UserGit extends Zend_Db_Table_Abstract
{

    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_name
    */
    protected $_name = 'usuarioGit';
    
    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_primary
    */
    protected $_primary = 'funcionario_idfuncionario';

    //Retorna true se existir um usuario git relacionado ao funcionario com id = $func_id
    
    /**
     * Funcao que Retorna true se existir um usuario BugZilla 
     * relacionado ao funcionario com id = $func_id
     * 
     * @access public 
     * @param int $func_id contendo o id do funcionario fornecido
     * @return true caso exista um usuario git relacionado ao funcionario com id = $func_id
     * @return false caso nao exista um usuario git relacionado ao funcionario com id = $func_id
     * 
     */
     public function existeUserGit($func_id)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('funcionario_idfuncionario = ?', $func_id);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }
	

}

