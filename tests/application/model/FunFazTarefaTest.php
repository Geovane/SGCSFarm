<?php
/* Classe responsável pelo acesso a tabela FunFazTarefa para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */

    class FunFazTarefa extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'funfaztarefa';
    	protected $_primary = 'tarefa_idtarefa';
    }
?>
    
<?php
/* Classe responsável pela realização dos testes da tabela funfaztarefa
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class FunFazTarefaTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;
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
            dirname(__FILE__) . '/_files/FunFazTarefaSeed.xml'
        );
    }
    
     /* Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testFunFazTarefaInsertedIntoDatabase()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name tarefaTable
         */
        $this->tarefaTable = new FunFazTarefa();
 
        /* Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
        $data = array(
            'tarefa_idtarefa' => '4',
            'colaboradores_idcolaboradores' => '3'
            );
 
       $this->tarefaTable->insert($data);
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('funfaztarefa', 'SELECT * FROM funfaztarefa');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/funfaztarefaInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testFunFazTarefaDelete()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $tarefaTable
         */
        $tarefaTable = new FunFazTarefa();
 
        $tarefaTable->delete(
            $tarefaTable->getAdapter()->quoteInto("tarefa_idtarefa = ?", 1)
        );
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($tarefaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/funfaztarefaDeleteAssertion.xml"),
            $ds
        );
    }
    
     /* Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testFunFazTarefaUpdate()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $tarefaTable
         */
        $tarefaTable = new FunFazTarefa();
 
         /* Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'colaboradores_idcolaboradores'      => '4'
        );
 
        /* Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $tarefaTable->getAdapter()->quoteInto('tarefa_idtarefa = ?', 1);
 
        $tarefaTable->update($data, $where);
 
        $rowset = $tarefaTable->fetchAll();
 
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
            dirname(__FILE__) . '/_files/funfaztarefaUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('funfaztarefa');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
