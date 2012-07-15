<?php
    class EmpresaFilial extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'empresafilial';
    	protected $_primary = 'idempresaFilial';
    }
?>
    
<?php
class EmpresaFilialTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/EmpresaFilialSeed.xml'
        );
    }
    
    public function testEmpresaFilialInsertedIntoDatabase()
    {
        $this->empresaTable = new EmpresaFilial();
 
        $data = array(
            'idempresaFilial' => '3',
            'nome' => 'SoftFarm - RUS',
            'tel' => '56 8945 9893',
            'endereco' => 'Russia',
            'responsavel' => '3',
            'empresa_idempresa' => '1',
            'email' => 'softfarmrus@gmail.com',
            'cep' => '23 678 123'
            );
 
       $this->empresaTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('empresafilial', 'SELECT * FROM empresafilial');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/empresafilialInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testEmpresaFilialDelete()
    {
        $empresaTable = new EmpresaFilial();
 
        $empresaTable->delete(
            $empresaTable->getAdapter()->quoteInto("idempresafilial = ?", 2)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($empresaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/empresafilialDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testEmpresaFilialUpdate()
    {
        $empresaTable = new EmpresaFilial();
 
        $data = array(
            'nome'      => 'SoftFarm',
            'email'      => 'softfarm@gmail.com'
        );
 
        $where = $empresaTable->getAdapter()->quoteInto('idempresafilial = ?', 1);
 
        $empresaTable->update($data, $where);
 
        $rowset = $empresaTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/empresafilialUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('empresafilial');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
