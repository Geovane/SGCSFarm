<?php
/**
 * Esta classe tem como objetivo efetuar a escolha da tabela 'dados_funcionario_filial'
 * no banco de dados.
 * 
 * @author SoftFarm
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 * 
 */
class Model_DbTable_FuncFilial extends Zend_Db_Table_Abstract
{

     /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_name
    */
    protected $_name = 'dados_funcionario_filial';
    
    /**
    * Variavel que recebe a chave primaria da tabela a ser acessada.
    * @access private
    * @name $_primary
    */
    protected $_primary = 'idfuncionario';


}
