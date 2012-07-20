<?php
/* Classe responsável pelo acesso a tabela projetogit para realização dos testes
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */

    class ProjGit extends Zend_Db_Table_Abstract
    {
    	protected $_name = 'projetogit';
    	protected $_primary = 'projeto_idprojeto';
    }
?>
    
<?php
/* Classe responsável pela realização dos testes da tabela projetogit
 * 
 * @author Mateus Passos
 * @version 0.1
 * @access public
 */
class ProjetoGitTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
            dirname(__FILE__) . '/_files/projetogitSeed.xml'
        );
    }
    
     /* Testa a inserção dos dados no banco
     * 
     * @access public
     * @return void
     */
    public function testProjetoGitInsertedIntoDatabase()
    {
        /* Variável que representa a tabela que se deseja testar
         * 
         * @name projTable
         */
        $this->projTable = new ProjGit();
 
        /* Variável que armazena os dados a serem inseridos no teste
         * 
         * @name $data
         */
        $data = array(
            'projeto_idprojeto' => '4',
            'repositorio' => 'Proj D',
            'chave' => 'asdajdajsk'
            );
 
       $this->projTable->insert($data);
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        
        $ds->addTable('projetogit', 'SELECT * FROM projetogit');
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__) . "/_files/projetogitInsertIntoAssertion.xml"),
            $ds
        );
    }
    
    /* Função que testa a exclusão de dados no banco
     * 
     * @access public
     * @return void
     */
     public function testProjetoGitDelete()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $projTable
         */
        $projTable = new ProjGit();
 
        $projTable->delete(
            $projTable->getAdapter()->quoteInto("projeto_idprojeto = ?", 1)
        );
 
        /* Variável que recebe a conexão com o banco de dados de testes
        * 
        * @name $ds
        */
        $ds = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $ds->addTable($projTable);
 
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/_files/projetoGitDeleteAssertion.xml"),
            $ds
        );
    }
    
     /* Função que testa a atualização de dados no banco
     * 
     * @access public
     * @return void
     */
    public function testProjetoGitUpdate()
    {
         /* Variável que representa a tabela que se deseja testar
         * 
         * @name $projTable
         */ 
        $projTable = new ProjGit();
 
         /* Variável que armazena os dados a serem atualizados no teste
         * 
         * @name $data
         */
        $data = array(
            'repositorio'      => 'Projeto Novo'
        );
 
        /* Variável que armazena qual elemento do banco será atualizado
         * 
         * @name $where
         */
        $where = $projTable->getAdapter()->quoteInto('projeto_idprojeto = ?', 2);
 
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
            dirname(__FILE__) . '/_files/projetogitUpdateAssertion.xml'
        );
        $expectedRowsets = $assertion->getTable('projetogit');
 
        $this->assertTablesEqual(
            $expectedRowsets, $ds
        );
    }
}

?>
