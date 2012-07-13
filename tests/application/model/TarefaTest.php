<?php
    class Tarefa extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'tarefa';
    	protected $_primary = 'idtarefa';
    }
?>
    
<?php
class TarefaTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/TarefaSeed.xml'
        );
    }
    
    public function testTarefaInsertedIntoDatabase()
    {
        $this->tarefaTable = new Tarefa();
 
        $data = array(
            'idtarefa' => '5',
            'descricao' => 'ronaldo',
            'dataInc' => '0000-00-00',
            'dataFim' => '1111-11-11',
            'estado_idestado' => '2',
            'dataEntrega' => '2012-01-01'
            );
 
       $this->tarefaTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('tarefa', 'SELECT * FROM tarefa');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/tarefaInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testTarefaDelete()
    {
        $tarefaTable = new Tarefa();
 
        $tarefaTable->delete(
            $tarefaTable->getAdapter()->quoteInto("idtarefa = ?", 1)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($tarefaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/tarefaDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testTarefaUpdate()
    {
        $tarefaTable = new Tarefa();
 
        $data = array(
            'descricao'      => 'Abiledebob'
        );
 
        $where = $tarefaTable->getAdapter()->quoteInto('idtarefa = ?', 2);
 
        $tarefaTable->update($data, $where);
 
        $rowset = $tarefaTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/tarefaUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('tarefa');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
