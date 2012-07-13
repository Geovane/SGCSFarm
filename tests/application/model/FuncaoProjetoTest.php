<?php
    class FuncaoProjeto extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'funcaoprojeto';
    	protected $_primary = 'idfuncaoProjeto';
    }
?>
    
<?php
class FuncaoProjetoTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/FuncaoProjetoSeed.xml'
        );
    }
    
    public function testFuncaoProjetoInsertedIntoDatabase()
    {
        $this->fprojTable = new FuncaoProjeto();
 
        $data = array(
            'idfuncaoProjeto' => '50',
            'descricao' => 'Procrastinador'
            );
 
       $this->fprojTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('funcaoprojeto', 'SELECT * FROM funcaoprojeto');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/funcaoprojetoInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testFuncaoProjetoDelete()
    {
        $fprojTable = new FuncaoProjeto();
 
        $fprojTable->delete(
            $fprojTable->getAdapter()->quoteInto("idfuncaoProjeto = ?", 10)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($fprojTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/funcaoprojetoDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testFuncaoProjetoUpdate()
    {
        $fprojTable = new FuncaoProjeto();
 
        $data = array(
            'descricao'      => 'Admnistrador'
        );
 
        $where = $fprojTable->getAdapter()->quoteInto('idfuncaoProjeto = ?', 10);
 
        $fprojTable->update($data, $where);
 
        $rowset = $fprojTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/funcaoprojetoUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('funcaoprojeto');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
