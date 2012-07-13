<?php
    class Estado extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'estado';
    	protected $_primary = 'idestado';
    }
?>
    
<?php
class EstadoTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/EstadoSeed.xml'
        );
    }
    
    public function testEstadoInsertedIntoDatabase()
    {
        $this->estadoTable = new Estado();
 
        $data = array(
            'idestado' => '8',
            'tipoDeEstado' => 'Descontinuado'
            );
 
       $this->estadoTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('estado', 'SELECT * FROM estado');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/estadoInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testEstadoDelete()
    {
        $estadoTable = new Estado();
 
        $estadoTable->delete(
            $estadoTable->getAdapter()->quoteInto("idestado = ?", 1)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($estadoTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/estadoDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testEstadoUpdate()
    {
        $estadoTable = new Estado();
 
        $data = array(
            'tipoDeEstado'      => 'Fase de elaboração'
        );
 
        $where = $estadoTable->getAdapter()->quoteInto('idestado = ?', 1);
 
        $estadoTable->update($data, $where);
 
        $rowset = $estadoTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/estadoUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('estado');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
