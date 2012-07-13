<?php
    class Colab extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'colaboradores';
    	protected $_primary = 'idcolaboradores';
    }
?>
    
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
                'dbname' => '2faseteste'
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
            dirname(__FILE__) . '/_files/ColaboradorSeed.xml'
        );
    }
    
    public function testColaboradorInsertedIntoDatabase()
    {
        $this->colabTable = new Colab();
 
        $data = array(
            'projeto_idprojeto' => '2',
            'funcionario_idfuncionario' => '5',
            'idcolaboradores' => '5',
            'dedicacaoMes' => '50',
            'funcaoProjeto_idFuncaoProjeto' => '10'
            );
 
       $this->colabTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('colaboradores', 'SELECT * FROM colaboradores');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/ColaboradorInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testColaboradorDelete()
    {
        $colabTable = new colab();
 
        $colabTable->delete(
            $colabTable->getAdapter()->quoteInto("idcolaboradores = ?", 3)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($colabTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/ColaboradorDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testColaboradorUpdate()
    {
        $colabTable = new colab();
 
        $data = array(
            'projeto_idprojeto'      => '3',
            'funcionario_idfuncionario'      => '2'
        );
 
        $where = $colabTable->getAdapter()->quoteInto('idcolaboradores = ?', 1);
 
        $colabTable->update($data, $where);
 
        $rowset = $colabTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/ColaboradorUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('colaboradores');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
