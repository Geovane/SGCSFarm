<?php

/**
 * Esta classe tem como objetivo efetuar a escolha da tabela 'funcaoprojeto'
 * no banco de dados.
 * 
 * @author SoftFarm
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 * 
 */
class Model_DbTable_FuncaoProjeto extends Zend_Db_Table_Abstract
{

    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_name
    */
    protected $_name = 'funcaoprojeto';
    
    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_primary
    */
    protected $_primary = 'idfuncaoProjeto';


}

