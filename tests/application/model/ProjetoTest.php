<?php
    class Proj extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'projeto';
    	protected $_primary = 'idprojeto';
    }
?>
    
<?php
class ProjetoTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/projetoSeed.xml'
        );
    }
    
    public function testProjetoInsertedIntoDatabase()
    {
        $this->projTable = new Proj();
 
        $data = array(
            'nome' => 'Proj E',
            'descricao' => 'Teste Tabela Projetos',
            'dataInc' => '2012-01-01',
            'dataFim' => '2013-01-01',
            'idGerente' => '1',
            'estado_idestado' => '1'
            );
 
       $this->projTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('projeto', 'SELECT * FROM projeto');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/projetoInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testProjetoDelete()
    {
        $projTable = new Proj();
 
        $projTable->delete(
            $projTable->getAdapter()->quoteInto("idprojeto = ?", 3)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($projTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/projetoDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testProjetoUpdate()
    {
        $projTable = new Proj();
 
        $data = array(
            'nome'      => 'Projeto A',
            'descricao'      => 'Um projeto sobre testes de Projetos'
        );
 
        $where = $projTable->getAdapter()->quoteInto('idprojeto = ?', 1);
 
        $projTable->update($data, $where);
 
        $rowset = $projTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/projetoUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('projeto');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
