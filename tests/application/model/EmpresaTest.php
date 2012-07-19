<?php
/* Classe responsável pelo acesso a tabela empresa para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */

    class Empresa extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'empresa';
    	protected $_primary = 'idempresa';
    }
?>
    
<?php
/* Classe responsável pela realização dos testes da tabela empresa
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class EmpresaTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/EmpresaSeed.xml'
        );
    }
    
     /* Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testEmpresaInsertedIntoDatabase()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name empresaTable
         */
        $this->empresaTable = new Empresa();
 
        /* Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
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
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('empresa', 'SELECT * FROM empresa');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/empresaInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testEmpresaDelete()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $empresaTable
         */ 
        $empresaTable = new empresa();
 
        $empresaTable->delete(
            $empresaTable->getAdapter()->quoteInto("idempresa = ?", 2)
        );
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($empresaTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/empresaDeleteAssertion.xml"),
            $ds
        );
    }
    
     /* Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testEmpresaUpdate()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $empresaTable
         */
        $empresaTable = new empresa();
 
         /* Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'nome'      => 'HardFarm',
            'email'      => 'hardfarm@gmail.com'
        );
 
        /* Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $empresaTable->getAdapter()->quoteInto('idempresa = ?', 1);
 
        $empresaTable->update($data, $where);
 
        $rowset = $empresaTable->fetchAll();
 
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
            dirname(__FILE__) . '/_files/empresaUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('empresa');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
