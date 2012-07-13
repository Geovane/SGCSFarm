<?php
/**
 * Esta classe tem como objetivo efetuar a conexão do sistema com a view 'estado_projeto'
 * no banco de dados, alem de prover os metodos de acesso ao banco implementados na classe Zend_Db_Table_Abstract
 * da qual esta classe herda suas carcteristicas
 *
 * @author SoftFarm
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 */
class Model_DbTable_EstadoProj extends Zend_Db_Table_Abstract
{
    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_name
    */
    protected $_name = 'estado_projeto';
    
    /**
    * Variavel que recebe o nome da tabela a ser acessada.
    * @access private
    * @name $_primary
    */
    protected $_primary = 'nomeProj';
    
    //
    
    /**
     * Funcao que busca o estado de um projeto pelo id.
     * 
     * @access public 
     * @param int $idProj contendo o id do projeto que se deseja o estado.
     * @return String[] contendo o estado atual daquele projeto com $idProj.
     * 
     */
     public function buscaEstadoProj($idProj)
    {
        $select = $this->select()
               ->where('idprojeto = ?', $idProj);

        return ($this->fetchRow($select)->tipoDeEstado);
    }
    
}

