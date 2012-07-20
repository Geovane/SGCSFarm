<?php
/** Classe responsável pelo acesso a tabela empresafilial para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
    class EmpresaFilial extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'empresafilial';
    	protected $_primary = 'idempresaFilial';
    }
?>
    
<?php
/** Classe responsável pela realização dos testes da tabela empresafilial
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class EmpresaFilialTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            /** Recebe os parâmetros para conexão com o banco
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
 
    /** Povoa a tabela que se deseja testar no banco de dados
     * 
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     * @access protected
     */
    protected function getDataSet()
    {
        return $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/_files/EmpresaFilialSeed.xml'
        );
    }
    
     /** Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testEmpresaFilialInsertedIntoDatabase()
    {
        /** Variável que representa a tabela que se deseja testar
         * 
         * @name empresaTable
         */
        $this->empresaTable = new EmpresaFilial();
 
        /** Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
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
 
        /** Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('empresafilial', 'SELECT * FROM empresafilial');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/empresafilialInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /** Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testEmpresaFilialDelete()
    {
         /** Variável que representa a tabela que se deseja testar
         * 
         * @name $empresaTable
         */
        $empresaTable = new EmpresaFilial();
 
        $empresaTable->delete(
            $empresaTable->getAdapter()->quoteInto("idempresafilial = ?", 2)
        );
 
        /** Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($empresaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/empresafilialDeleteAssertion.xml"),
            $ds
        );
    }
    
     /** Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testEmpresaFilialUpdate()
    {
         /** Variável que representa a tabela que se deseja testar
         * 
         * @name $empresaTable
         */
        $empresaTable = new EmpresaFilial();
 
         /** Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'nome'      => 'SoftFarm',
            'email'      => 'softfarm@gmail.com'
        );
 
        /** Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $empresaTable->getAdapter()->quoteInto('idempresafilial = ?', 1);
 
        $empresaTable->update($data, $where);
 
        $rowset = $empresaTable->fetchAll();
 
       /** Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds        = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset);
        
        /** Variável responsável por receber o arquivo que fará a verificação dos dados no banco
         * 
         * @name $assertion
         */
        
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
