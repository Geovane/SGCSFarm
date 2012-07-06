<?php

class Model_Permissoes
{

    static function responsavelEmpresa( $idFunc, $idEmpresa) {

        $empresa = new Model_DbTable_Empresa();

        //Verificando se o funcionario é responsavel pela empresa passada;
        $select = $empresa->select()
                ->from('empresa', 'COUNT(*) AS num')
                ->where('responsavelGeral = ?', $idFunc)
                ->where('idempresa = ?', $idEmpresa);


         return ($empresa->fetchRow($select)->num == 0) ? false : true;

    }

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

