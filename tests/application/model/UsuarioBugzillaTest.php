<?php
    class UsuarioBugzilla extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'usuariobugzilla';
    	protected $_primary = 'funcionario_idfuncionario';
    }
?>
    
<?php
class UsuarioBugzillaTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/usuariobugzillaSeed.xml'
        );
    }
    
    public function testUsuarioBugzillaInsertedIntoDatabase()
    {
        $this->userTable = new UsuarioBugzilla();
 
        $data = array(
            'funcionario_idfuncionario' => '5',
            'usuario' => 'SMoreno',
            'senha' => '123'
            );
 
       $this->userTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('usuariobugzilla', 'SELECT * FROM usuariobugzilla');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/usuariobugzillaInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testUsuarioBugzillaDelete()
    {
        $userTable = new UsuarioBugzilla();
 
        $userTable->delete(
            $userTable->getAdapter()->quoteInto("funcionario_idfuncionario = ?", 1)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($userTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/usuariobugzillaDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testUsuarioBugzillaUpdate()
    {
        $userTable = new UsuarioBugzilla();
 
        $data = array(
            'senha'      => '123456'
        );
 
        $where = $userTable->getAdapter()->quoteInto('funcionario_idfuncionario = ?', 2);
 
        $userTable->update($data, $where);
 
        $rowset = $userTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/usuariobugzillaUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('usuariobugzilla');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
