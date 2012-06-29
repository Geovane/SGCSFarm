<?php
/**
 * Esta classe tem como objetivo efetuar o CRUD de Tarefas para um projeto.
 * Ela contém os recursos necessários para a associação de uma tarefa recém
 * criada a um colaborador alocado em um projeto.
 * 
 * Os actions deste controller são: index, create, edit, delete e prepara.
 * 
 *@author Bruno Pereira dos Santos
 *@version 0.1
 *@access public
 *
 */
class TarefaController extends Zend_Controller_Action
{
    
    /** 
     * Função que inicializa todos os parametros necessários para o correto
     * funcionamento dos actions.
     * 
     * @access public 
     * @return void
     */
    public function init()
    {
        
        //Verifica se o usuario esta autenticado, 
        //caso não esteja ele é redirecionado para a tela da login.
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }
        
        
        //Variável que recebe as informações do usuario logado no sistema.
        $this->funcLogado = Zend_Auth::getInstance()->getIdentity();
        $this->view->funcLogado = $this->funcLogado;
        
        
        /**
         * Variáveis responsáveis pelo acesso as tabelas do banco de dado.
         * 
         * @name funFazTarefa
         * @name tarefa
         * @name estado
         * @name projeto
         * @name colaboradores
         * @name funcionario
         * @name tarColabProj
         * @access disponível em todos os actions do controller Tarefas
         */
        $this->funFazTarefa = new Application_Model_DbTable_FunFazTarefa();
        $this->tarefa = new Application_Model_DbTable_Tarefa();
        $this->estado = new Application_Model_DbTable_Estado();
        $this->projeto = new Model_DbTable_Proj();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->funcionario = new Model_DbTable_Func();
        $this->tarColabProj = new Model_DbTable_TarColabProj();
        
        
    }
    
    /** 
     * Função inicial do controller tarefas.
     * envia ao seu view as informações sobre os projetos.
     * 
     * @access public 
     * @return void
     */
    public function indexAction()
    {
        // action body
        
        $select = $this->projeto->select();
        $this->view->projeto = $this->projeto->fetchAll($select);
        
    }
    
    /** 
     * Função responsável pela inserção ao banco dos dados de uma nova tarefa
     * criada vinculada a um colaborador em um projeto.
     * 
     * @access public 
     * @return void
     */
    public function createAction()
    {
        // action body
        $idProj = $this->_getParam('idProj');
        $result  = $this->projeto->find($idProj);
        $this->view->projetoEncontrado = $result->current();
        
        
        $idColab = $this->_getParam('idColab');
        $this->view->idColab = $idColab;
        
        //Efetua uma busca pelo id do colaborador, para encontrar a FK_funcionario
        //com a FK_funcionario é possível encontrar o nome do funcionário
        $where = $this->colaboradores->getAdapter()->quoteInto('idcolaboradores = ?', $idColab);
        $select = $this->colaboradores->select()
                ->where($where);
        $colabEncontrado = $this->colaboradores->fetchRow($select);
        $fk_funcionario = $colabEncontrado->funcionario_idfuncionario;
        
        $result = $this->funcionario->find($fk_funcionario);
        $this->view->funcionario = $result->current();
        
        $this->view->estadoTarefa = $this->estado->fetchAll();

        if( $this->getRequest()->isPost() ) {
            /**
             * Variável que pega uma data enviada pelo método post e prepara o
             * array para posterior inserção no bando de dados.
             *  
             * @name $dataInc
             */
            $dataInc = $this->inverte_data($this->_request->getPost('dataInc'), "/");
            $dataInc = $dataInc." ".date("H:i:s");
            
            /**
             * Variável que pega uma data enviada pelo método post e prepara o
             * array para posterior inserção no bando de dados.
             *  
             * @name $dataFim
             */
            $dataFim = $this->inverte_data($this->_request->getPost('dataFim'), "/");
            $dataFim = $dataFim." ".date("H:i:s");
            
            /**
             * Variável responsável pelos dados a serem inseridos na tabela
             * tarefas do banco de dados
             * 
             * o campo 'estado_idestado' deve sempre ser setada para o estado
             * inicial, pois a tarefa acada de ser criada
             * 
             * O campo 'dataEntrega' deve ser omitido, pois nada foi entregue
             * 
             * @name $dados
             */
            $dados = array(
                'descricao'  => $this->_request->getPost('descricao'),
                'dataInc'  => $dataInc,
                'dataFim' => $dataFim,
                'estado_idestado' => '2',//Tarefa sempre inicia em "estado inicial"
                'dataEntrega'  => ''
            );
            
            /**
             * Efetua inserçao no banco.
             * A variavel '$idInseridoTarefa' contém o id do linha inserida.
             * O parâmetro '$dados' contém os dados das colunas da tabela tarefas.
             * 
             * @name $idInseridoTarefa
             * @param $dados
             */
            
            $idInseridoTarefa = $this->tarefa->insert($dados);
            
            /**
             * Variável responsável pelos dados a serem inseridos na tabela
             * funFazTarefa do banco de dados
             * 
             * @name $dadosFunFazTar
             */
            $dadosFunFazTar = array(
                'tarefa_idtarefa'  => $idInseridoTarefa,
                'colaboradores_idcolaboradores'  => $idColab
            );
            
            /**
             * Efetua inserçao no banco.
             * A variavel '$idInseridoFunFazTar' contém o id do linha inserida.
             * O parâmetro '$dadosFunFazTar' contém os dados das colunas da tabela
             * funFazTarefa.
             * 
             * @name $idInseridoFunFazTar
             * @param $dadosFunFazTar
             */
            $idInseridoFunFazTar = $this->funFazTarefa->insert($dadosFunFazTar);
            
            //verifica se algum dos inserts obeteve erro e envia a informação
            // de erro ou não com o parâmetro 'flag'
            if(($idInseridoFunFazTar == null) || ($idInseridoTarefa == null)){
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
            }else{
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/1');
            }
        }
        
    }
    
    /** 
     * Função responsável pela edição de uma tarefa existente associada a um
     * colaborador em um projeto.
     * 
     * @access public 
     * @return void
     */
    public function editAction()
    {
        // action body
        $idProj = $this->_getParam('idProj');
        $result  = $this->projeto->find($idProj);
        $this->view->projetoEncontrado = $result->current();
        
        
        $idColab = $this->_getParam('idColab');
        $this->view->idColab = $idColab;
        
        //Efetua uma busca pelo id do colaborador, para encontrar a FK_funcionario
        //com a FK_funcionario é possível encontrar o nome do funcionário
        $where = $this->colaboradores->getAdapter()->quoteInto('idcolaboradores = ?', $idColab);
        $select = $this->colaboradores->select()
                ->where($where);
        $colabEncontrado = $this->colaboradores->fetchRow($select);
        $fk_funcionario = $colabEncontrado->funcionario_idfuncionario;
        
        $result  = $this->funcionario->find($fk_funcionario);
        $this->view->funcionario = $result->current();
        
        
        $idTarefa = $this->_getParam('idTarefa');
        $result  = $this->tarefa->find($idTarefa);
        $this->view->id = $idTarefa;
        $this->view->tarefaEncontrada = $result->current();
        $this->view->estadoTarefa = $this->estado->fetchAll();
        
        if( $this->getRequest()->isPost() ) {
            
            $dataInc = $this->inverte_data($this->_request->getPost('dataInc'), "/");
            $dataInc = $dataInc." ".date("H:i:s");

            $dataFim = $this->inverte_data($this->_request->getPost('dataFim'), "/");
            $dataFim = $dataFim." ".date("H:i:s");
            
            /**
             * Variável que pega uma data enviada pelo método post e prepara o
             * array para posterior inserção no bando de dados.
             *  
             * @name $dataEntrega
             */
            $dataEntrega = $this->inverte_data($this->_request->getPost('dataEntrega'), "/");
            $dataEntrega = $dataEntrega." ".date("H:i:s");

            $dados = array(
                'descricao'  => $this->_request->getPost('descricao'),
                'dataInc'  => $dataInc,
                'dataFim' => $dataFim,
                'estado_idestado' => $this->_request->getPost('estadoTarefa'),
                'dataEntrega'  => $dataEntrega
            );
            
            /**
             * Efetua a atualização no banco.
             * A variavel '$where' contém a condição de atualização.
             * A variavel '$idAtualizado' contém o id do linha atualizada.
             * O parâmetro '$dados' contém os dados das colunas da tabela
             * tarefa.
             * 
             * @name $where
             * @name $idAtualizado
             * @param $dados
             */
            $where = $this->tarefa->getAdapter()->quoteInto("idtarefa = ?", $idTarefa);
            $idAtualizado = $this->tarefa->update($dados, $where);
            
            //verifica se a atualização obeteve erro e envia a informação
            //de erro ou não com o parâmetro 'flag'
            if($idAtualizado == null){
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
            }else{
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/2');
            }
        }
    }
    
    /** 
     * Função responsável pela deleção de uma tarefa existente associada a um
     * colaborador em um projeto.
     * 
     * @access public 
     * @return void
     */
    public function deleteAction()
    {
        // action body
        $idTarefa = $this->_getParam('idTarefa');
        $idColab = $this->_getParam('idColab');
        
        /**
        * Efetua a deleção no banco de dados da tabela funFazTarefa.
        * A variavel '$where' contém a condição de deleção.
        * A variavel '$idDelatadoFunfazTar' contém o id do linha deletada.
        * 
        * @name $where
        * @name $idDelatadoFunfazTar
        */
        $where = $this->funFazTarefa->getAdapter()->quoteInto('tarefa_idtarefa = ?', $idTarefa, 'colaboradores_idcolaboradores = ?', $idColab);
        $idDelatadoFunfazTar = $this->funFazTarefa->delete($where);
        
        /**
        * Efetua a deleção no banco de dados da tabela tarefa.
        * A variavel '$where' contém a condição de deleção.
        * A variavel '$idDelatadoTar' contém o id do linha deletada.
        * 
        * @name $where
        * @name $idDelatadoTar
        */
        $where = $this->tarefa->getAdapter()->quoteInto('idtarefa = ?', $idTarefa);
        $idDelatadoTar = $this->tarefa->delete($where);
        
        $idProj = $this->_getParam('idProj');
        
        //verifica se a atualização obeteve erro e envia a informação
            //de erro ou não com o parâmetro 'flag'
        if(($idDelatadoFunfazTar == null) || ($idDelatadoTar == null)){
            $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
        }else{
            $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/3');
        }
    }
    
    /** 
     * Função para a inversão de datas.
     * Exemplo dd/mm/yyyy é convertido em yyyy/mm/dd e vice-versa
     * recebe a data e o tipo de separador utilizado
     * retorna a data invertida.
     * 
     * @access private 
     * @param String $data
     * @param String $separador
     * @return string $nova_data
     */ 
    private function inverte_data($data, $separador)
    {
        $nova_data = implode("".$separador."",array_reverse(explode("".$separador."",$data)));
        return $nova_data;
    }
    
    /** 
     * Função responsável pela preparação de criação de uma nova tarefa associada a um
     * colaborador em um projeto.
     * Visualização de tarefas existentes em um projeto.
     * 
     * @access public 
     * @return void
     */
    public function preparaAction()
    {
        // action body
        $this->view->flag = $this->_request->getParam('flag');
        $listProj = $this->_getParam('idProj');
        
        //verifica se algum projeto foi selecionado e presenta as informações
        //do mesmo.
        if( ($this->getRequest()->isPost()) || ($listProj != null) ){
            if($listProj == null)
                $listProj = $this->_request->getPost('listProj');
            
            $idColab = $this->_request->getPost('listColab');
            $idProj = $this->_request->getPost('idProj');
            
            //redireciona para a criação de uma tarefa, pois já foi selecionado
            //um projeto e uma tarefa.
            if(($idColab != null) && ($idProj != null)){
                $this->_redirect('/tarefa/create/idProj/'.$idProj.'/idColab/'.$idColab.'');
            }
            //caso nenhum projeto tenha sido selecionado redireciona para a pagina
            //de tarefas, onde é possível selecionar um projeto.
            if($listProj == null){
                $this->_redirect('/tarefa');
            }else{
                //exibe as tarefas daquele projeto.
                
                $result  = $this->projeto->find($listProj);
                $this->view->projEncontrado = $result->current();                
                
                $where = $this->colaboradores->getAdapter()->quoteInto('projeto_idprojeto = ?', $listProj);
                $select = $this->colaboradores->select()
                        ->where($where);
                $this->view->listaColaboradores = $this->colaboradores->fetchAll($select);
                
                $select = $this->funcionario->select();
                $this->view->listaFuncionarios = $this->funcionario->fetchAll($select);
                
                $select = $this->estado->select();
                $this->view->listaEstado = $this->estado->fetchAll($select);
                
                
                $where = $this->projeto->getAdapter()->quoteInto('idprojeto = ?', $listProj);
                $select = $this->projeto->select()
                        ->where($where);
                $projEncontrado = $this->projeto->fetchRow($select);
                $projEncontrado = $projEncontrado->nome;
                
                $where = $this->projeto->getAdapter()->quoteInto('nomeProj = ?', $projEncontrado);
                $select = $this->tarColabProj->select()
                        ->where($where);
                $rows = $this->tarColabProj->fetchAll($select);
                
                //Cria a paginação relativa a exibição dos funcionarios
                $paginator = Zend_Paginator::factory($rows);
                //Passa o numero de registros por pagina
                $paginator->setItemCountPerPage(5);

                $this->view->paginator = $paginator;
                $paginator->setCurrentPageNumber($this->_getParam('page'));
                
            }
            
        }
    }

}
