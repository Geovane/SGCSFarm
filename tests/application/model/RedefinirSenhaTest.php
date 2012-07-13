<?php
    class RedefinirSenha extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'redefinirsenha';
    	protected $_primary = 'funcionario_idfuncionario';
    }
?>
    
<?php
class RedefinirSenhaTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/redefinirsenhaSeed.xml'
        );
    }
    
    public function testRedefinirSenhaInsertedIntoDatabase()
    {
        $this->senhaTable = new RedefinirSenha();
 
        $data = array(
            'hash' => 'ascasdascasd',
            'funcionario_idfuncionario' => '4'
            );
 
       $this->senhaTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('redefinirsenha', 'SELECT * FROM redefinirsenha');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/redefinirsenhaInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testRedefinirSenhaDelete()
    {
        $senhaTable = new RedefinirSenha();
 
        $senhaTable->delete(
            $senhaTable->getAdapter()->quoteInto("funcionario_idfuncionario = ?", 1)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($senhaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/redefinirsenhaDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testRedefinirSenhaUpdate()
    {
        $senhaTable = new RedefinirSenha();
 
        $data = array(
            'hash'      => 'asdgascbmadshjk'
        );
 
        $where = $senhaTable->getAdapter()->quoteInto('funcionario_idfuncionario = ?', 2);
 
        $senhaTable->update($data, $where);
 
        $rowset = $senhaTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/redefinirsenhaUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('redefinirsenha');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
