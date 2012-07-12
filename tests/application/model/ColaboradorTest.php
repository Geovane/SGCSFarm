<?php
class ColaboradorTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;
 
    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if($this->_connectionMock == null) {
            $connection = Zend_Db::factory('Pdo_Mysql', array(
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'dbname' => '2fase'
            ));
            $connection -> query("SET foreign_key_checks = 0");
            $this->_connectionMock = $this->createZendDbConnection(
                $connection, 'zfunittests'
            );
            Zend_Db_Table_Abstract::setDefaultAdapter($connection);
        }
        return $this->_connectionMock;
    }
 
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/colaboradorSeed.xml'
        );
    }
    
    public function testColaboradoresInsertedIntoDatabase()
    {
        $colabTable = new Model_DbTable_Colaboradores();
 
        $data = array(
            'projeto_idprojeto' => '1',
            'funcionario_idfuncionario' => '27',
            'dedicacaoMes' => '30',
            'funcaoProjeto' => '10'
            );
 
        $colabTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('colaboradores', 'SELECT * FROM colaboradores');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/colaboradorInsertIntoAssertion.xml"),
            $ds
        );
    }
}

?>
