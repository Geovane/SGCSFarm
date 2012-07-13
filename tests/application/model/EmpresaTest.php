<?php
    class Empresa extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'empresa';
    	protected $_primary = 'idempresa';
    }
?>
    
<?php
class empresaoradorTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/EmpresaSeed.xml'
        );
    }
    
    public function testEmpresaInsertedIntoDatabase()
    {
        $this->empresaTable = new Empresa();
 
        $data = array(
            'idempresa' => '2',
            'nome' => 'SoftFarm Hardware',
            'tel' => '7332316572',
            'email' => 'softfarmhardware@gmail.com',
            'site' => 'www.softfarm.com',
            'cep' => '45 650 000',
            'endereco' => 'Avenida Soares Lopes, 23, Centro Ilhéus',
            'rezaoSocial' => '8321983091741',
            'responsavelGeral' => '3'
            );
 
       $this->empresaTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('empresa', 'SELECT * FROM empresa');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/empresaInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testempresaoradorDelete()
    {
        $empresaTable = new empresa();
 
        $empresaTable->delete(
            $empresaTable->getAdapter()->quoteInto("idempresa = ?", 2)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($empresaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/empresaoradorDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testempresaoradorUpdate()
    {
        $empresaTable = new empresa();
 
        $data = array(
            'nome'      => 'HardFarm',
            'email'      => 'hardfarm@gmail.com'
        );
 
        $where = $empresaTable->getAdapter()->quoteInto('idempresa = ?', 1);
 
        $empresaTable->update($data, $where);
 
        $rowset = $empresaTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/empresaoradorUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('empresa');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
