<?php
/* Classe responsável pelo acesso a tabela Funcionario para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */

    class Func extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'funcionario';
    	protected $_primary = 'idfuncionario';
    }
?>
    
<?php
/* Classe responsável pela realização dos testes da tabela funcionario
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class FuncionarioTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/funcionarioSeed.xml'
        );
    }
    
     /* Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testFuncionarioInsertedIntoDatabase()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name funcTable
         */
        $this->funcTable = new Func();
 
        /* Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
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
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('funcionario', 'SELECT * FROM funcionario');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/funcionarioInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testFuncionarioDelete()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name $funcTable
         */
        $funcTable = new Func();
 
        $funcTable->delete(
            $funcTable->getAdapter()->quoteInto("idfuncionario = ?", 4)
        );
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($funcTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/funcionarioDeleteAssertion.xml"),
            $ds
        );
    }
    
     /* Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testFuncionarioUpdate()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $funcTable
         */
        $funcTable = new Func();
 
         /* Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'nome'      => 'Pedro Augusto',
            'login'      => 'PAugusto'
        );
 
        /* Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $funcTable->getAdapter()->quoteInto('idfuncionario = ?', 1);
 
        $funcTable->update($data, $where);
 
        $rowset = $funcTable->fetchAll();
 
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
            dirname(__FILE__) . '/_files/funcionarioUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('funcionario');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
