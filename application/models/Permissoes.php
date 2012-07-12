<?php
/**
 * Esta classe tem como objetivo efetuar a escolha da tabela 'projetobugzilla'
 * no banco de dados.
 * 
 * @author Geovane
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 * 
 */
class Model_Permissoes
{
    
    /**
     * Funcao que Retorna true o o funcionario e responsavel pela empresa informada
     * @author Geovane
     * @access public 
     * @param int $idFunc contendo o id do funcionario fornecido
     * @param int $idEmpresa contendo o id da empresa
     * @return true se $idFunc seja responsavel pela empresa com id $idEmpresa.
     * @return false caso nao $idFunc seja responsavel pela empresa com id $idEmpresa.
     * 
     */
    static function responsavelEmpresa( $idFunc, $idEmpresa) {

        $empresa = new Model_DbTable_Empresa();

        //Verificando se o funcionario é responsavel pela empresa passada;
        $select = $empresa->select()
                ->from('empresa', 'COUNT(*) AS num')
                ->where('responsavelGeral = ?', $idFunc)
                ->where('idempresa = ?', $idEmpresa);


         return ($empresa->fetchRow($select)->num == 0) ? false : true;

    }

    /**
     * Funcao que Retorna true o o funcionario e responsavel pela empresa filial
     * @author Geovane
     * @access public 
     * @param int $idFunc contendo o id do funcionario fornecido
     * @param int $idFilial contendo o id da empresa filial
     * @return true se $idFunc seja responsavel pela empresa filial com id $idFilial.
     * @return false caso nao $idFunc seja responsavel pela empresa filial com id $idFilial.
     * 
     */
    static function responsavelFilial( $idFunc, $idFilial) {

        $filial = new Model_DbTable_Filial();

        //Verificando se o funcionario é responsavel pela filial passada;
        $select = $filial->select()
                ->from('empresafilial', 'COUNT(*) AS num')
                ->where('responsavel = ?', $idFunc)
                ->where('idempresaFilial = ?', $idFilial);


       return ($filial->fetchRow($select)->num == 0) ? false : true;

    }

}

