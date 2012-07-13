<?php
    class UsuarioGit extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'usuariogit';
    	protected $_primary = 'funcionario_idfuncionario';
    }
?>
    
<?php
class UsuarioGitTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/UsuarioGitSeed.xml'
        );
    }
    
    public function testUsuarioGitInsertedIntoDatabase()
    {
        $this->userTable = new UsuarioGit();
 
        $data = array(
            'funcionario_idfuncionario' => '5',
            'usuario' => 'SMoreno',
            'senha' => '123'
            );
 
       $this->userTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('usuariogit', 'SELECT * FROM usuariogit');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/UsuarioGitInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testUsuarioGitDelete()
    {
        $userTable = new UsuarioGit();
 
        $userTable->delete(
            $userTable->getAdapter()->quoteInto("funcionario_idfuncionario = ?", 1)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($userTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/UsuarioGitDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testUsuarioGitUpdate()
    {
        $userTable = new UsuarioGit();
 
        $data = array(
            'senha'      => '123456'
        );
 
        $where = $userTable->getAdapter()->quoteInto('funcionario_idfuncionario = ?', 2);
 
        $userTable->update($data, $where);
 
        $rowset = $userTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/UsuarioGitUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('usuariogit');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
