<?php
/* Classe responsável pelo acesso a tabela Colaboradores para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */

    class Colab extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'colaboradores';
    	protected $_primary = 'idcolaboradores';
    }
?>
    
<?php
/* Classe responsável pela realização dos testes da tabela colaboradores
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class ColaboradorTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/ColaboradorSeed.xml'
        );
    }
    
    /* Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testColaboradorInsertedIntoDatabase()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name colabTable
         */
        $this->colabTable = new Colab();
 
        /* Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
        $data = array(
            'projeto_idprojeto' => '2',
            'funcionario_idfuncionario' => '5',
            'idcolaboradores' => '5',
            'dedicacaoMes' => '50',
            'funcaoProjeto_idFuncaoProjeto' => '10'
            );
 
       $this->colabTable->insert($data);
 
       /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('colaboradores', 'SELECT * FROM colaboradores');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/ColaboradorInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testColaboradorDelete()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $colabTable
         */
        $colabTable = new colab();
 
        $colabTable->delete(
            $colabTable->getAdapter()->quoteInto("idcolaboradores = ?", 3)
        );
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($colabTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/ColaboradorDeleteAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testColaboradorUpdate()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $colabTable
         */
        $colabTable = new colab();
 
         /* Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'projeto_idprojeto'      => '3',
            'funcionario_idfuncionario'      => '2'
        );
 
        /* Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $colabTable->getAdapter()->quoteInto('idcolaboradores = ?', 1);
 
        $colabTable->update($data, $where);
 
        $rowset = $colabTable->fetchAll();
 
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
            dirname(__FILE__) . '/_files/ColaboradorUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('colaboradores');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
