<?php
/* Classe responsável pelo acesso a tabela redefinirsenha para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */

    class RedefinirSenha extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'redefinirsenha';
    	protected $_primary = 'funcionario_idfuncionario';
    }
?>
    
<?php
/* Classe responsável pela realização dos testes da tabela redefinirsenha
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class RedefinirSenhaTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/redefinirsenhaSeed.xml'
        );
    }
    
     /* Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testRedefinirSenhaInsertedIntoDatabase()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name senhaTable
         */
        $this->senhaTable = new RedefinirSenha();
 
        /* Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
        $data = array(
            'hash' => 'ascasdascasd',
            'funcionario_idfuncionario' => '4'
            );
 
       $this->senhaTable->insert($data);
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('redefinirsenha', 'SELECT * FROM redefinirsenha');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/redefinirsenhaInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testRedefinirSenhaDelete()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name $senhaTable
         */
        $senhaTable = new RedefinirSenha();
 
        $senhaTable->delete(
            $senhaTable->getAdapter()->quoteInto("funcionario_idfuncionario = ?", 1)
        );
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($senhaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/redefinirsenhaDeleteAssertion.xml"),
            $ds
        );
    }
    
     /* Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testRedefinirSenhaUpdate()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name $senhaTable
         */
        $senhaTable = new RedefinirSenha();
 
         /* Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'hash'      => 'asdgascbmadshjk'
        );
 
        /* Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $senhaTable->getAdapter()->quoteInto('funcionario_idfuncionario = ?', 2);
 
        $senhaTable->update($data, $where);
 
        $rowset = $senhaTable->fetchAll();
 
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
            dirname(__FILE__) . '/_files/redefinirsenhaUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('redefinirsenha');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
