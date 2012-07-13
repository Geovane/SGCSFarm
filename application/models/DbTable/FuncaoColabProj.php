<?php
/**
 * Esta classe tem como objetivo efetuar a conexão do sistema com a view 'funcao_colaboradores_projeto'
 * no banco de dados, alem de prover os metodos de acesso ao banco implementados na classe Zend_Db_Table_Abstract
 * da qual esta classe herda suas carcteristicas
 *
 * @author SoftFarm
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 */
class Model_DbTable_FuncaoColabProj extends Zend_Db_Table_Abstract
{

    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_name
    */
    protected $_name = 'funcao_colaboradores_projeto';
    
    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_primary
    */
    protected $_primary = 'nomeProj';
}