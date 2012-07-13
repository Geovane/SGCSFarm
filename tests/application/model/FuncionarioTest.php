<?php
    class Func extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'funcionario';
    	protected $_primary = 'idfuncionario';
    }
?>
    
<?php
class FuncionarioTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/funcionarioSeed.xml'
        );
    }
    
    public function testFuncionarioInsertedIntoDatabase()
    {
        $this->funcTable = new Func();
 
        $data = array(
            'nome' => 'Joao de Piracicaba',
            'documentoIdentificacao' => '112269',
            'login' => 'JPira',
            'senha' => '123',
            'email' => 'joaopiracicaba@gmail.com',
            'empresaFilial_idempresaFilial' => '1',
            'primeiroAcesso' => '0',
            'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
            );
 
       $this->funcTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('funcionario', 'SELECT * FROM funcionario');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/funcionarioInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testFuncionarioDelete()
    {
        $funcTable = new Func();
 
        $funcTable->delete(
            $funcTable->getAdapter()->quoteInto("idfuncionario = ?", 4)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($funcTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/funcionarioDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testFuncionarioUpdate()
    {
        $funcTable = new Func();
 
        $data = array(
            'nome'      => 'Pedro Augusto',
            'login'      => 'PAugusto'
        );
 
        $where = $funcTable->getAdapter()->quoteInto('idfuncionario = ?', 1);
 
        $funcTable->update($data, $where);
 
        $rowset = $funcTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/funcionarioUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('funcionario');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
