<?php

/**
 * Esta classe tem como objetivo efetuar o CRUD de Tarefas para um projeto.
 * Ela contem os recursos necessarios para a associacao de uma tarefa recem
 * criada a um colaborador alocado em um projeto.
 * 
 * Os actions deste controller sao: index, create, edit, delete e prepara.
 * 
 * @author Bruno Pereira dos Santos
 * @version 0.1
 * @access public
 * 
 * 
 */
 
class TarefaController extends Zend_Controller_Action
{

    /**
     * Funcao que inicializa todos os parametros necessarios para o correto
     * funcionamento dos actions.
     * 
     * @access public 
     * @return void
     * 
     */
    public function init()
    {
        //Verifica se o usuario esta autenticado, 
        //caso nao esteja ele e redirecionado para a tela da login.
        //Verifica se o usuario esta autenticado, caso nao esteja ele e redirecionado para a tela da login
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }

        //Pega as informacoes do usuario logado no sistema.
        $this->funcLogado = Zend_Auth::getInstance()->getIdentity();
        //Envia pra view
        $this->view->funcLogado = $this->funcLogado;

        //Informacoes de exibicao do usuario no index (deve estar em todos os inits)
        $this->FuncFilial = new Model_DbTable_FuncFilial();
        $dadosIndex = $this->FuncFilial->find($this->funcLogado->idfuncionario);
        $this->view->dadosIndex = $dadosIndex[0];

        //Dados do usuario logado para serem utilizados nas actions
        $this->idFunc = $this->funcLogado->idfuncionario;
        $this->idEmpresa = $dadosIndex[0]->empresa_idempresa;
        $this->idFilial = $this->funcLogado->empresaFilial_idempresaFilial;

        $idFunc = $this->idFunc;
        $idFilial = $this->idFilial;
        $idEmpresa =  $this->idEmpresa;

        //Informacoes relativas a permissoes (Se tiver permissao retorna True)
        $this->adminFilial = Model_Permissoes::responsavelFilial($idFunc,$idFilial);
        $this->adminEmpresa = Model_Permissoes::responsavelEmpresa($idFunc,$idEmpresa);

        $this->view->AdminFilial = $this->adminFilial;
        $this->view->AdminEmpresa = $this->adminEmpresa;
        /**
        * Variaveis responsaveis pelo acesso as tabelas do banco de dado.
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
        $this->estado = new Model_DbTable_Estado();
        $this->projeto = new Model_DbTable_Proj();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->funcionario = new Model_DbTable_Func();
        $this->tarColabProj = new Model_DbTable_TarColabProj();
    }

    /**
     * Funcao responsavel pela insercao ao banco dos dados de uma nova tarefa
     * criada vinculada a um colaborador em um projeto.
     * 
     * @access public 
     * @return void
     * 
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
        //com a FK_funcionario e possível encontrar o nome do funcionario
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
                * Variavel que pega uma data enviada pelo metodo post e prepara o
                * array para posterior insercao no bando de dados.
                *  
                * @name $dataInc
                */
            $dataInc = $this->inverte_data($this->_request->getPost('dataInc'), "/");
            $dataInc = $dataInc." ".date("H:i:s");

            /**
                * Variavel que pega uma data enviada pelo metodo post e prepara o
                * array para posterior insercao no bando de dados.
                *  
                * @name $dataFim
                */
            $dataFim = $this->inverte_data($this->_request->getPost('dataFim'), "/");
            $dataFim = $dataFim." ".date("H:i:s");
            
            $where = $this->estado->getAdapter()->quoteInto('tipoDeEstado = ?', "Fase inicial");
            $select = $this->estado->select()
                        ->where($where);
            $idTarEncontrada = $this->estado->fetchRow($select);
            $idTarEncontrada = $idTarEncontrada->idestado;
            /**
                * Variavel responsavel pelos dados a serem inseridos na tabela
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
                'estado_idestado' => $idTarEncontrada,//Tarefa sempre inicia em "estado inicial"
                'dataEntrega'  => ''
            );
            
            /**
                * Efetua insercao no banco.
                * A variavel '$idInseridoTarefa' contem o id do linha inserida.
                * O parâmetro '$dados' contem os dados das colunas da tabela tarefas.
                * 
                * @name $idInseridoTarefa
                * @param $dados
                */

            $idInseridoTarefa = $this->tarefa->insert($dados);

            /**
                * Variavel responsavel pelos dados a serem inseridos na tabela
                * funFazTarefa do banco de dados
                * 
                * @name $dadosFunFazTar
                */
            $dadosFunFazTar = array(
                'tarefa_idtarefa'  => $idInseridoTarefa,
                'colaboradores_idcolaboradores'  => $idColab
            );

            /**
                * Efetua insercao no banco.
                * A variavel '$idInseridoFunFazTar' contem o id do linha inserida.
                * O parâmetro '$dadosFunFazTar' contem os dados das colunas da tabela
                * funFazTarefa.
                * 
                * @name $idInseridoFunFazTar
                * @param $dadosFunFazTar
                */
            $idInseridoFunFazTar = $this->funFazTarefa->insert($dadosFunFazTar);

            //verifica se algum dos inserts obeteve erro e envia a informacao
            // de erro ou nao com o parâmetro 'flag'
            if(($idInseridoFunFazTar == null) || ($idInseridoTarefa == null)){
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
            }else{
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/1');
            }
        }
    }

    /**
     * Funcao responsavel pela edicao de uma tarefa existente associada a um
     * colaborador em um projeto.
     * 
     * @access public 
     * @return void
     * 
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
        //com a FK_funcionario e possível encontrar o nome do funcionario
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
                * Variavel que pega uma data enviada pelo metodo post e prepara o
                * array para posterior insercao no bando de dados.
                *  
                * @name $dataEntrega
                */
            //$dataEntrega = $this->inverte_data($this->_request->getPost('dataEntrega'), "/");
            //$dataEntrega = $dataEntrega." ".date("H:i:s");
            $dataEntrega = "";
            $idEstado = $this->_request->getPost('estadoTarefa');
            $select = $this->estado->select()
                        ->where('idestado = ?', $idEstado);
            $nomeEstado = $this->estado->fetchRow($select);
            if($nomeEstado->tipoDeEstado == "Concluido"){
                $dataEntrega = "".date("Y/m/d");
                $dataEntrega = $dataEntrega." ".date("H:i:s");
            }
            $dados = array(
                'descricao'  => $this->_request->getPost('descricao'),
                'dataInc'  => $dataInc,
                'dataFim' => $dataFim,
                'estado_idestado' => $this->_request->getPost('estadoTarefa'),
                'dataEntrega'  => $dataEntrega
            );

            /**
                * Efetua a atualizacao no banco.
                * A variavel '$where' contem a condicao de atualizacao.
                * A variavel '$idAtualizado' contem o id do linha atualizada.
                * O parâmetro '$dados' contem os dados das colunas da tabela
                * tarefa.
                * 
                * @name $where
                * @name $idAtualizado
                * @param $dados
                */
            $where = $this->tarefa->getAdapter()->quoteInto("idtarefa = ?", $idTarefa);
            $idAtualizado = $this->tarefa->update($dados, $where);

            //verifica se a atualizacao obeteve erro e envia a informacao
            //de erro ou nao com o parâmetro 'flag'
            if($idAtualizado == null){
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
            }else{
                $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/2');
            }
        }
    }

    /**
     * Funcao responsavel pela delecao de uma tarefa existente associada a um
     * colaborador em um projeto.
     * 
     * @access public 
     * @return void
     * 
     */
    public function deleteAction()
    {
        // action body
        $idTarefa = $this->_getParam('idTarefa');
        $idColab = $this->_getParam('idColab');

        /**
        * Efetua a delecao no banco de dados da tabela funFazTarefa.
        * A variavel '$where' contem a condicao de delecao.
        * A variavel '$idDelatadoFunfazTar' contem o id do linha deletada.
        * 
        * @name $where
        * @name $idDelatadoFunfazTar
        */
        $where = $this->funFazTarefa->getAdapter()->quoteInto('tarefa_idtarefa = ?', $idTarefa, 'colaboradores_idcolaboradores = ?', $idColab);
        $idDelatadoFunfazTar = $this->funFazTarefa->delete($where);

        /**
        * Efetua a delecao no banco de dados da tabela tarefa.
        * A variavel '$where' contem a condicao de delecao.
        * A variavel '$idDelatadoTar' contem o id do linha deletada.
        * 
        * @name $where
        * @name $idDelatadoTar
        */
        $where = $this->tarefa->getAdapter()->quoteInto('idtarefa = ?', $idTarefa);
        $idDelatadoTar = $this->tarefa->delete($where);

        $idProj = $this->_getParam('idProj');

        //verifica se a atualizacao obeteve erro e envia a informacao
            //de erro ou nao com o parâmetro 'flag'
        if(($idDelatadoFunfazTar == null) || ($idDelatadoTar == null)){
            $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/4');
        }else{
            $this->_redirect('/tarefa/prepara/idProj/'.$idProj.'/flag/3');
        }
    }

    /**
     * Funcao para a inversao de datas.
     * Exemplo dd/mm/yyyy e convertido em yyyy/mm/dd e vice-versa
     * recebe a data e o tipo de separador utilizado
     * retorna a data invertida.
     * 
     * @access private 
     * @param String $data
     * @param String $separador
     * @return string $nova_data
     * 
     */
    private function inverte_data($data, $separador)
    {
        $nova_data = implode("".$separador."",array_reverse(explode("".$separador."",$data)));
        return $nova_data;
    }

    /**
     * Funcao responsavel pela preparacao de criacao de uma nova tarefa
     * associada a um
     * colaborador em um projeto.
     * Visualizacao de tarefas existentes em um projeto.
     * 
     * @access public 
     * @return void
     * 
     */
    public function preparaAction()
    {
        // action body
        $this->view->flag = $this->_request->getParam('flag');
        $listProj = $this->_getParam('idProj');
        
        $where = $this->projeto->getAdapter()->quoteInto('idprojeto = ?', $listProj);
        $select = $this->projeto->select()
                    ->where($where);
        $projEncontrado = $this->projeto->fetchRow($select);
        $idGerente = $projEncontrado->idGerente;
        $idFuncLogado = $this->funcLogado->idfuncionario;
        
        if($idGerente != $idFuncLogado){
            $this->_redirect('/projeto/');
        }
        
        //verifica se algum projeto foi selecionado e presenta as informacoes
        //do mesmo.
        if( ($this->getRequest()->isPost()) || ($listProj != null) ){
            if($listProj == null)
                $listProj = $this->_request->getPost('listProj');

            $idColab = $this->_request->getPost('listColab');
            $idProj = $this->_request->getPost('idProj');

            //redireciona para a criacao de uma tarefa, pois ja foi selecionado
            //um projeto e uma tarefa.
            if(($idColab != null) && ($idProj != null)){
                $this->_redirect('/tarefa/create/idProj/'.$idProj.'/idColab/'.$idColab.'');
            }
            //caso nenhum projeto tenha sido selecionado redireciona para a pagina
            //de tarefas, onde e possível selecionar um projeto.
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

                //Cria a paginacao relativa a exibicao dos funcionarios
                $paginator = Zend_Paginator::factory($rows);
                //Passa o numero de registros por pagina
                $paginator->setItemCountPerPage(3);

                $this->view->paginator = $paginator;
                $paginator->setCurrentPageNumber($this->_getParam('page'));

            }
        }
    }
    
    /**
     * Funcao responsavel pela busca de tarefas de um determinado colaborador
     * associado a um projeto.
     * Visualizacao de tarefas existentes em um projeto.
     * 
     * @access public 
     * @return void
     * 
     */
    public function minhasTarefasAction()
    {
        // action body
        $idFunc = $this->funcLogado->idfuncionario;
        $idProj = $this->_getParam('idProj');
        
        $select = $this->estado->select();
        $this->view->listaEstado = $this->estado->fetchAll($select);
        
        $where = $this->colaboradores->getAdapter()->quoteInto('projeto_idprojeto = ?', $idProj);
        $where1 = $this->colaboradores->getAdapter()->quoteInto('funcionario_idfuncionario = ?', $idFunc);
        $select = $this->colaboradores->select()
                    ->where($where)
                    ->where($where1);
        $colabEncontrado = $this->colaboradores->fetchRow($select);
        $idColabEncontrado = $colabEncontrado->idcolaboradores;  
        
        
        $where = $this->projeto->getAdapter()->quoteInto('idprojeto = ?', $idProj);
        $select = $this->projeto->select()
                    ->where($where);
        $projEncontrado = $this->projeto->fetchRow($select);
        $this->view->projEncontrado = $projEncontrado;
        $nomeProjEncontrado = $projEncontrado->nome;
        
        $select = $this->tarColabProj->select()
                        ->where('nomeProj = ?', $nomeProjEncontrado)
                        ->where('idColab = ?', $idColabEncontrado);
        
        $rows = $this->tarColabProj->fetchAll($select);
        
        //Cria a paginacao relativa a exibicao dos funcionarios
        $paginator = Zend_Paginator::factory($rows);
        //Passa o numero de registros por pagina
        $paginator->setItemCountPerPage(5);

        $this->view->paginator = $paginator;
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        
    }


}


