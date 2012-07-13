<?php
/**
 * Esta classe tem como objetivo efetuar a conexão do sistema com a tabela 'redefinirSenha'
 * no banco de dados, alem de prover os metodos de acesso ao banco implementados na classe Zend_Db_Table_Abstract
 * da qual esta classe herda suas carcteristicas
 *
 * @author SoftFarm
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 */
class Model_DbTable_RedefinirSenha extends Zend_Db_Table_Abstract
{
    
    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_name
    */
    protected $_name = 'redefinirSenha';
    
    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_primary
    */
    protected $_primary = 'hash';

    /**
     * Metodo que Retorna true se existir alguma solicitacao de
     * redefinicão de senha para o funcionario com id = $func_id.
     *
     * @author Geovane Mimoso
     * @access public 
     * @param int $func_id com o id do funcionario que se deseja redefinir a senha.
     * @return true caso exista alguma solicitacao de 
     * redefinicao
     * @return false caso nao exista alguma solicitacao de 
     * redefinicao
     * 
     */
    public function existeRedefinir($func_id)
    {
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) AS num')
               ->where('funcionario_idfuncionario = ?', $func_id);

        return ($this->fetchRow($select)->num == 0) ? false : true;
    }
    
}

