<?php
/**
 * Esta classe tem como objetivo efetuar o CRUD de Projetos para um projeto
 * bem como permitir acessos a views de gerentes (geral e de projetos).
 * Ela contém os recursos necessários para o controle dos usuários ao CRUDE e
 * as visões de cada tipo de usuário.
 * 
 * @author Bruno Pereira dos Santos
 * @author Matheus Passos
 * @version 0.1
 * @access public
 * 
 * 
 */



class ProjetoController extends Zend_Controller_Action
{
    
    /**
     * Função que inicializa todos os parametros necessários para o correto
     * funcionamento dos actions.
     * 
     * @access public 
     * @return void
     * 
     */
    public function init()
    {
        //Verifica se o usuario esta autenticado, caso não esteja ele é redirecionado para a tela da login
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }

        //Pega as informações do usuario logado no sistema.
        $this->funcLogado = Zend_Auth::getInstance()->getIdentity();
        //Envia pra view
        $this->view->funcLogado = $this->funcLogado;

        //Informações de exibição do usuario no index (deve estar em todos os inits)
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

        //Informações relativas a permissoes (Se tiver permissão retorna True)
        $this->adminFilial = Model_Permissoes::responsavelFilial($idFunc,$idFilial);
        $this->adminEmpresa = Model_Permissoes::responsavelEmpresa($idFunc,$idEmpresa);

        $this->view->AdminFilial = $this->adminFilial;
        $this->view->AdminEmpresa = $this->adminEmpresa;        

        $this->project = new Model_DbTable_Proj();
        $this->funcionario = new Model_DbTable_Func();
        $this->estado = new Model_DbTable_Estado();
        $this->projbugzilla = new Model_DbTable_ProjBugzilla();
        $this->projgit = new Model_DbTable_ProjGit();
        $this->funcaoproj = new Model_DbTable_FuncaoProjeto();
        $this->colab = new Model_DbTable_Colaboradores();
        $this->empresafilial = new Model_DbTable_Filial();
        $this->funcaoColabProj = new Model_DbTable_FuncaoColabProj();
        $this->tipoEstadoTarefaColabProj = new Model_DbTable_TipoEstadoTarefaColabProj();
        $this->colabProj = new Model_DbTable_ColaboradoresProjetos();
        $this->ProjGerFiliColab = new Model_DbTable_ProjGerFiliColab();
        $this->empresa = new Model_DbTable_Empresa();
        $this->filial = new Model_DbTable_Filial();
    }
    
    /**
     * Função inicial do controller projetos.
     * envia ao seu view as informações sobre os projetos.
     * 
     * @access public 
     * @return void
     * 
     */
    public function indexAction()
    {
        $idFuncLogado = $this->funcLogado->idfuncionario;
        $this->view->idFuncLogado = $idFuncLogado;

        $selectProjs = $this->ProjGerFiliColab->select()
                ->from(array('p' => 'projetos_gerente_filial_colaboradores'),
                        array('idprojeto', 'nomeProj', 'dataFim', 'dataInc', 'estadoProj', 'idGerente',
                    'nomeGerente', 'descricaoProj'))
                ->distinct()
                ->where('idGerente = ?', $idFuncLogado)
                ->orWhere('idFuncionarioColaborador = ?', $idFuncLogado);

        $rows = $this->ProjGerFiliColab->fetchAll($selectProjs);

        $paginator = Zend_Paginator::factory($rows);
        //Passa o numero de registros por pagina
        $paginator->setItemCountPerPage(4);

        $this->view->paginator = $paginator;
        $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    public function gerencioAction()
    {
        $idFuncLogado = $this->funcLogado->idfuncionario;
        $this->view->idFuncLogado = $idFuncLogado;

        $selectProjs = $this->ProjGerFiliColab->select()
                ->from(array('p' => 'projetos_gerente_filial_colaboradores'),
                        array('idprojeto', 'nomeProj', 'dataFim', 'dataInc', 'estadoProj', 'idGerente',
                    'nomeGerente', 'descricaoProj'))
                ->distinct()
                ->where('idGerente = ?', $idFuncLogado)
                ->orWhere('idFuncionarioColaborador = ?', $idFuncLogado);

        $rows = $this->ProjGerFiliColab->fetchAll($selectProjs);

        $paginator = Zend_Paginator::factory($rows);
        //Passa o numero de registros por pagina
        $paginator->setItemCountPerPage(4);

        $this->view->paginator = $paginator;
        $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    
    
    /**
     * Função responsável pela inserção ao banco dos dados de uma novo projeto
     * 
     * @access public 
     * @return void
     * 
     */
    public function createAction()
    {
        $select = $this->funcionario->select();
        $select->where('empresaFilial_idempresaFilial = ?', $this->idFilial);
        $select->order('nome');
        $this->view->funcionario = $this->funcionario->fetchAll($select);
                                                               
        if($this->_request->isPost())    
        {
            /**
            * Variável que pega uma data enviada pelo método post e prepara o
            * array para posterior inserção no bando de dados.
            *  
            * @name $dataInc
            */
            $dataInc = $this->inverte_data($this->_request->getPost('dtinicio'), "/");
            $dataInc = $dataInc." ".date("H:i:s");
            
            $selectnome = $this->project->select();
            $selectnome -> where('nome = ?', $this->_request->getPost('nome'));
            
            $nomeprojeto = $this->project->fetchRow($selectnome)->nome;
            
            if($nomeprojeto == $this->_request->getPost('nome'))
            {
                $this->_redirect('projeto/index/flag/2');
            }    

            $data = array
            (
                'nome' => $this->_request->getPost('nome'),
                'descricao' => $this->_request->getPost('descricao'),
                'dataInc' => $dataInc,
                'dataFim' => "",
                'idGerente' => $this->_request->getPost('idGerente'),
                'estado_idestado' => (int)2
            );

            $idprojetoinserido = $this->project->insert($data);

            $data1 = array
            (
                'projeto_idprojeto' => $idprojetoinserido,
                'nomeProjeto' => $this->_request->getPost('nome')  
            );

            $this->projbugzilla->insert($data1);

            $data2 = array
            (
                'projeto_idprojeto' => $idprojetoinserido,
                'repositorio' => $this->_request->getPost('nome'),
                'chave' => "asdasdsda"
            );

            $this->projgit->insert($data2);

            $this->_redirect('projeto/index');
        }
    }
    
    /**
     * Função responsável pela edição de projeto existente
     * 
     * @access public 
     * @return void
     * 
     */
    public function editAction()
    {
        $id_proj = $this->_getParam('id');
        
        $selectdata = $this->project->select();
        $selectdata -> where('idprojeto = ?',$id_proj);
        
        $datainc = $this->inverte_data($this->project->fetchRow($selectdata)->dataInc, '-');

        $result = $this->project->find($id_proj);
        $this->view->datainc = $datainc;
        $this->view->projeto = $result->current();
        $this->view->funcionario = $this->funcionario->fetchAll();
        $this->view->estado = $this->estado->fetchAll();

        if($this->_request->isPost())
        {
            $datafim = $this->inverte_data($this->_request->getPost('dtfim'), '/');
            $data = array
            (
                'descricao' => $this->_request->getPost('descricao'),              
                'dataFim' => $datafim,                
                'estado_idestado' => $this->_request->getPost('estado')  
            );

            $where = $this->project->getAdapter()->quoteInto('idprojeto = ?', (int) $this->_request->getPost('id'));

            $this->project->update($data,$where);

            $this->_redirect('projeto/index');
        }
    }
    
    
    /**
     * Função responsável pela deleção de um projeto existente
     * 
     * @access public 
     * @return void
     * 
     */
    public function deleteAction()
    {
        //Precisa só colocar as flags informando o ocorrido para o usuário
        $id_proj = $this->_getParam('id');

        $select = $this->project->select();
        $select -> from($this->project, 'COUNT(*) AS num')
                -> where('idprojeto = ?', $id_proj)
                ->where('estado_idestado = 7');

        if($this->project->fetchRow($select)->num != 0)
        {
            $where = $this->projbugzilla->getAdapter()->quoteInto('projeto_idprojeto = ?',$id_proj);
            $this->projbugzilla->delete($where);

            $where = $this->projgit->getAdapter()->quoteInto('projeto_idprojeto = ?',$id_proj);
            $this->projgit->delete($where);

            $where = $this->project->getAdapter()->quoteInto('idprojeto = ?',$id_proj);
            $this->project->delete($where);

            $this->_redirect('projeto/index');
        }else{
            $this->_redirect('projeto/index');
        }
    }
    
    
    /**
     * Função responsável pela exibição detalhada de um projeto
     * 
     * @access public 
     * @return void
     * 
     */
    public function detalhesAction()
    {
        $idFuncLogado = $this->funcLogado->idfuncionario;
        $this->view->idFuncLogado = $idFuncLogado;
        
        $idProj = $this->_getParam('idProj');
        $where = $this->ProjGerFiliColab->getAdapter()->quoteInto('idprojeto = ?', $idProj);
        $selecProj = $this->ProjGerFiliColab->select()
                ->where($where)
                ->where('idFuncionarioColaborador = ?', $idFuncLogado)
                ->orWhere('idGerente = ?', $idFuncLogado );
        
        $validaUsuario = $this->ProjGerFiliColab->fetchRow($selecProj);
        
        if( ($validaUsuario == null)){
            $validaUsuario = '0';
        }else{
            $validaUsuario = '1';
        }
        
        $this->view->naoColabEnaoEGerente = $validaUsuario;
        
        $where = $this->ProjGerFiliColab->getAdapter()->quoteInto('idprojeto = ?', $idProj);
        $selecProj = $this->ProjGerFiliColab->select()
                ->where($where);
        $ProjEncontrado = $this->ProjGerFiliColab->fetchAll($selecProj);
        $this->view->ProjEncontrado = $ProjEncontrado;

        $nomeProj = $ProjEncontrado->current()->nomeProj;
        $selectEstadoTarefa = $this->tipoEstadoTarefaColabProj->select()
                    ->where('nomeProj = ?', $nomeProj);
        $rows = $this->tipoEstadoTarefaColabProj->fetchAll($selectEstadoTarefa);

        $paginator = Zend_Paginator::factory($rows);
        //Passa o numero de registros por pagina
        $paginator->setItemCountPerPage(4);

        $this->view->paginator = $paginator;
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $selecColab = $this->colabProj->select()
                ->where('nomeProj = ?', $nomeProj);
        $this->view->colab = $this->colabProj->fetchAll($selecColab);
    }
    
    /**
     * Função responsável pela exibição de todos os projetos daquela filial
     * para o administrador responsável pela filial.
     * 
     * @access public 
     * @return void
     * 
     */
    public function adminfilialAction(){
        $idFuncLogado = $this->funcLogado->idfuncionario;
        
        $selecFilial = $this->filial->select()
                ->where('responsavel = ?', $idFuncLogado);
        $filialEncontrada = $this->filial->fetchRow($selecFilial);
        
        if($filialEncontrada->responsavel == $idFuncLogado){
            $this->view->nomeFilial = $filialEncontrada->nome;
            $idFilial = $filialEncontrada->idempresaFilial;
            $selectProjs = $this->ProjGerFiliColab->select()
                ->from(array('p' => 'projetos_gerente_filial_colaboradores'),
                        array('idprojeto', 'nomeProj', 'dataFim', 'dataInc', 'estadoProj', 'idGerente',
                    'nomeGerente', 'descricaoProj'))
                ->distinct()
                ->where('idFilialProj = ?', $idFilial);

            $rows = $this->ProjGerFiliColab->fetchAll($selectProjs);

            $paginator = Zend_Paginator::factory($rows);
            //Passa o numero de registros por pagina
            $paginator->setItemCountPerPage(4);

            $this->view->paginator = $paginator;
            $paginator->setCurrentPageNumber($this->_getParam('page'));
            
        }else{
            $this->_redirect('/');
        }
        
    }
    
    /**
     * Função responsável pela exibição de todos os projetos daquela empresa
     * para o administrador geral da empresa.
     * 
     * @access public 
     * @return void
     * 
     */
    public function admingeralAction(){
        
        $idFuncLogado = $this->funcLogado->idfuncionario;
                
        $selecEmpresa = $this->empresa->select()
                ->where('responsavelGeral = ?', $idFuncLogado);
        $empresaEncontrada = $this->empresa->fetchRow($selecEmpresa);
        
        if($empresaEncontrada->responsavelGeral == $idFuncLogado){
            $this->view->nomeEmpresa = $empresaEncontrada->nome;
            $selectProjs = $this->ProjGerFiliColab->select()
                    ->from(array('p' => 'projetos_gerente_filial_colaboradores'),
                            array('idprojeto', 'nomeProj', 'dataFim', 'dataInc', 'estadoProj', 'idGerente',
                        'nomeGerente', 'descricaoProj'))
                    ->distinct();

            $rows = $this->ProjGerFiliColab->fetchAll($selectProjs);

            $paginator = Zend_Paginator::factory($rows);
            //Passa o numero de registros por pagina
            $paginator->setItemCountPerPage(4);

            $this->view->paginator = $paginator;
            $paginator->setCurrentPageNumber($this->_getParam('page'));


        }else{
            $this->_redirect('/');
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
     * 
     * 
     */
    private function inverte_data($data, $separador)
    {
        $nova_data = implode("".$separador."",array_reverse(explode("".$separador."",$data)));
                        return $nova_data;
    }

    
}







