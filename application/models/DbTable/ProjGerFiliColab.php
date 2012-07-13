<?php
/**
 * Esta classe tem como objetivo efetuar a conexão do sistema com a view 'projetos_gerente_filial_colaboradores'
 * no banco de dados, alem de prover os metodos de acesso ao banco implementados na classe Zend_Db_Table_Abstract
 * da qual esta classe herda suas carcteristicas
 *
 * @author SoftFarm
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 */
class Model_DbTable_ProjGerFiliColab extends Zend_Db_Table_Abstract
{

    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_name
    */
    protected $_name = 'projetos_gerente_filial_colaboradores';
    
    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_primary
    */
    protected $_primary = 'nomeProj';
}