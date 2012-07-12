<?php
/**
 * Esta classe tem como objetivo efetuar a escolha da tabela 'funcionario'
 * no banco de dados.
 * 
 * @author SoftFarm
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 * 
 */
class Model_DbTable_Func extends Zend_Db_Table_Abstract
{

    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_name
    */
    protected $_name = 'funcionario';
    
    /**
    * Variavel que recebe a chave primaria da tabela a ser acessada.
    * @access private
    * @name $_primary
    */
    protected $_primary = 'idfuncionario';
    
    /**
     * Funcao que Retorna true se ja existir o login cadastrado
     * funcionamento dos actions.
     * 
     * @access public 
     * @param String[] $login contendo o login fornecido
     * @return true caso exista um login cadastrado
     * @return false caso nao exista um login cadastrado
     * 
     */
    public function existeLogin($login)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('login = ?', $login);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }

    /**
     * Funcao que Retorna true se ja existir o documento de identificacao cadastrado
     * funcionamento dos actions.
     * 
     * @access public 
     * @param Int $doc contendo o numero de identificacao inserido.
     * @return true caso exista um documento de identificacao cadastrado
     * @return false caso nao exista um documento de identificacao cadastrado
     * 
     */
    public function existeDoc($doc)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('documentoIdentificacao = ?', $doc);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }
	

}

