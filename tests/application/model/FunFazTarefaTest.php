<?php
    class FunFazTarefa extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'funfaztarefa';
    	protected $_primary = 'tarefa_idtarefa';
    }
?>
    
<?php
class FunFazTarefaTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/FunFazTarefaSeed.xml'
        );
    }
    
    public function testFunFazTarefaInsertedIntoDatabase()
    {
        $this->tarefaTable = new FunFazTarefa();
 
        $data = array(
            'tarefa_idtarefa' => '4',
            'colaboradores_idcolaboradores' => '3'
            );
 
       $this->tarefaTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('funfaztarefa', 'SELECT * FROM funfaztarefa');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/funfaztarefaInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testFunFazTarefaDelete()
    {
        $tarefaTable = new FunFazTarefa();
 
        $tarefaTable->delete(
            $tarefaTable->getAdapter()->quoteInto("tarefa_idtarefa = ?", 1)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($tarefaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/funfaztarefaDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testFunFazTarefaUpdate()
    {
        $tarefaTable = new FunFazTarefa();
 
        $data = array(
            'colaboradores_idcolaboradores'      => '4'
        );
 
        $where = $tarefaTable->getAdapter()->quoteInto('tarefa_idtarefa = ?', 1);
 
        $tarefaTable->update($data, $where);
 
        $rowset = $tarefaTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/funfaztarefaUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('funfaztarefa');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
