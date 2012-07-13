<?php
/**
 * Classe responsável pelos metodos que verificam as permissões de usuarios no sistema.
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
     * Funcao que verifica se o funcionario ($idFunc) é responsavel pela empresa ($idEmpresa)
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
     * Funcao que verifica se o funcionario ($idFunc) é responsavel pela filial ($idFilial)
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

