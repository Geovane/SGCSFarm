<?php
/* Classe responsável pelo acesso a tabela projeto para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */

    class Proj extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'projeto';
    	protected $_primary = 'idprojeto';
    }
?>
    
<?php
/* Classe responsável pela realização dos testes da tabela projeto
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class ProjetoTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
  /**
     * Retorna a conexão para o banco de dados de teste.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     * @access protected
     */
    protected function getConnection()
    {
        if($this->_connectionMock == null) {
            /* Recebe os parâmetros para conexão com o banco
             * 
             * @name $connection
             */
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
 
    /* Povoa a tabela que se deseja testar no banco de dados
     * 
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     * @access protected
     */
    protected function getDataSet()
    {
        return $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/projetoSeed.xml'
        );
    }
    
     /* Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testProjetoInsertedIntoDatabase()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name projTable
         */
        $this->projTable = new Proj();
 
        /* Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
        $data = array(
            'nome' => 'Proj E',
            'descricao' => 'Teste Tabela Projetos',
            'dataInc' => '2012-01-01',
            'dataFim' => '2013-01-01',
            'idGerente' => '1',
            'estado_idestado' => '1'
            );
 
       $this->projTable->insert($data);
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('projeto', 'SELECT * FROM projeto');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/projetoInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testProjetoDelete()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $projTable
         */
        $projTable = new Proj();
 
        $projTable->delete(
            $projTable->getAdapter()->quoteInto("idprojeto = ?", 3)
        );
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($projTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/projetoDeleteAssertion.xml"),
            $ds
        );
    }
    
     /* Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testProjetoUpdate()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $projTable
         */ 
        $projTable = new Proj();
 
         /* Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'nome'      => 'Projeto A',
            'descricao'      => 'Um projeto sobre testes de Projetos'
        );
 
        /* Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $projTable->getAdapter()->quoteInto('idprojeto = ?', 1);
 
        $projTable->update($data, $where);
 
        $rowset = $projTable->fetchAll();
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        
        /* Variável responsável por receber o arquivo que fará a verificação dos dados no banco
         * 
         * @name $assertion
         */
        
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
