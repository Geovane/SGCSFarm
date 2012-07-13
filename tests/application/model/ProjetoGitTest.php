<?php
    class ProjGit extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'projetogit';
    	protected $_primary = 'projeto_idprojeto';
    }
?>
    
<?php
class ProjetoGitTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/projetogitSeed.xml'
        );
    }
    
    public function testProjetoGitInsertedIntoDatabase()
    {
        $this->projTable = new ProjGit();
 
        $data = array(
            'projeto_idprojeto' => '4',
            'repositorio' => 'Proj D',
            'chave' => 'asdajdajsk'
            );
 
       $this->projTable->insert($data);
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('projetogit', 'SELECT * FROM projetogit');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/projetogitInsertIntoAssertion.xml"),
            $ds
        );
    }
    
     public function testProjetoGitDelete()
    {
        $projTable = new ProjGit();
 
        $projTable->delete(
            $projTable->getAdapter()->quoteInto("projeto_idprojeto = ?", 1)
        );
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($projTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/projetoGitDeleteAssertion.xml"),
            $ds
        );
    }
    
    public function testProjetoGitUpdate()
    {
        $projTable = new ProjGit();
 
        $data = array(
            'repositorio'      => 'Projeto Novo'
        );
 
        $where = $projTable->getAdapter()->quoteInto('projeto_idprojeto = ?', 2);
 
        $projTable->update($data, $where);
 
        $rowset = $projTable->fetchAll();
 
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        $assertion = $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/projetogitUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('projetogit');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
