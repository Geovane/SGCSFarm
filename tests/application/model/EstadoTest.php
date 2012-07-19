<?php
/* Classe responsável pelo acesso a tabela Estado para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */

    class Estado extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'estado';
    	protected $_primary = 'idestado';
    }
?>
    
<?php
/* Classe responsável pela realização dos testes da tabela estado
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class EstadoTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/EstadoSeed.xml'
        );
    }
    
     /* Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testEstadoInsertedIntoDatabase()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name estadoTable
         */
        $this->estadoTable = new Estado();
 
        /* Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
        $data = array(
            'idestado' => '8',
            'tipoDeEstado' => 'Descontinuado'
            );
 
       $this->estadoTable->insert($data);
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('estado', 'SELECT * FROM estado');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/estadoInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testEstadoDelete()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $estadoTable
         */
        $estadoTable = new Estado();
 
        $estadoTable->delete(
            $estadoTable->getAdapter()->quoteInto("idestado = ?", 1)
        );
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($estadoTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/estadoDeleteAssertion.xml"),
            $ds
        );
    }
    
     /* Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testEstadoUpdate()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $estadoTable
         */
        $estadoTable = new Estado();
 
         /* Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'tipoDeEstado'      => 'Fase de elaboração'
        );
 
        /* Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $estadoTable->getAdapter()->quoteInto('idestado = ?', 1);
 
        $estadoTable->update($data, $where);
 
        $rowset = $estadoTable->fetchAll();
 
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
            dirname(__FILE__) . '/_files/estadoUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('estado');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
