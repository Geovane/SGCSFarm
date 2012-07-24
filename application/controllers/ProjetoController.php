<?php
/**
 * Esta classe tem como objetivo efetuar o CRUD de Projetos para um projeto
 * bem como permitir acessos a views de gerentes (geral e de projetos).
 * Ela contem os recursos necessarios para o controle dos usuarios ao CRUDE e
 * as visoes de cada tipo de usuario.
 * 
 * @author Bruno Pereira dos Santos
 * @author Matheus Passos
 * @version 0.1
 * @access public
 * @copyright Copyright © 2012, SoftFarm.
 * 
 */



class ProjetoController extends Zend_Controller_Action
{
    

     /**
     * Funcao que inicializa todos os parametros necessarios para o correto
     * funcionamento dos actions, como conexões com o banco de dados e
     * variaveis de controle dos actions, alem de enviar para as views, as informaçoes de sessão e
     * de permissões de usuarios.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function init()
    {
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
        $this->estadoProj = new Model_DbTable_EstadoProj();
        $this->projFiliais = new Model_DbTable_ProjetosFiliais();
    }
    
    /**
     * Funcao inicial do controller projetos.
     * envia ao seu view as informacoes sobre os projetos.
     * 
     * @access public 
     * @return void
     * 
     */
    public function indexAction()
    {
        $page = $this->_request->getParam('page');
            if ( isset($page) ){
                $this->view->mem = true;
            }
        
        $this->view->flag = $this->_request->getParam('flag');
        $idFuncLogado = $this->funcLogado->idfuncionario;
        $this->view->idFuncLogado = $idFuncLogado;

        $selectProjs = $this->ProjGerFiliColab->select()
                ->from(array('p' => 'projetos_gerente_filial_colaboradores'),
                        array('idprojeto', 'nomeProj', 'dataFim', 'dataInc', 'estadoProj', 'idGerente',
                    'nomeGerente', 'descricaoProj'))
                ->distinct()
                ->where('idFuncionarioColaborador = ?', $idFuncLogado);

        $rows = $this->ProjGerFiliColab->fetchAll($selectProjs);

        $paginator = Zend_Paginator::factory($rows);
        //Passa o numero de registros por pagina
        $paginator->setItemCountPerPage(4);

        $this->view->paginator = $paginator;
        $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    
    /**
     * Funcao responsavel pela exibicao dos projetos que um dado funcionario
     * esta alocado como gerente.
     * Envia para seus views as informacoes destes projetos.
     * 
     * @access public 
     * @return void
     * 
     */
    public function gerencioAction()
    {
        $idFuncLogado = $this->funcLogado->idfuncionario;
        $this->view->idFuncLogado = $idFuncLogado;
        $this->view->estadoProj = $this->estadoProj;        
        $selectProjs = $this->project->select()
                ->distinct()
                ->where('idGerente = ?', $idFuncLogado);

        $rows = $this->project->fetchAll($selectProjs);

        $paginator = Zend_Paginator::factory($rows);
        //Passa o numero de registros por pagina
        $paginator->setItemCountPerPage(4);

        $this->view->paginator = $paginator;
        $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    
    
    /**
     * Funcao responsavel pela insercao ao banco dos dados de uma novo projeto
     * 
     * @access public 
     * @return void
     * 
     */
    public function createAction()
    {
        //Verifica permissão
        if(!$this->adminFilial){
            $this->_redirect('/index/negado');
        }

        $select = $this->funcionario->select();
        $select->where('empresaFilial_idempresaFilial = ?', $this->idFilial);
        $select->order('nome');
        $this->view->funcionario = $this->funcionario->fetchAll($select);
                                                               
        if($this->_request->isPost())    
        {
            /**
            * Variavel que pega uma data enviada pelo metodo post e prepara o
            * array para posterior insercao no bando de dados.
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
                $this->view->mensagem = "<div id='alerta'>Ja existe um projeto com este nome!</div>";
            }else{    

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

            $this->_redirect('projeto/index/flag/3');
        }
      }
    }
    
    /**
     * Funcao responsavel pela edicao de projeto existente
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
            if($this->_request->getPost('estado') != 7)
            {    
            $data = array
            (
                'descricao' => $this->_request->getPost('descricao'),
                'dataFim' => '0001/01/01',
                'estado_idestado' => $this->_request->getPost('estado')  
            );
            }else
            {
                $data = array
                (
                'descricao' => $this->_request->getPost('descricao'),
                'dataFim' => date("Y/m/d"),    
                'estado_idestado' => $this->_request->getPost('estado') 
                );
            }    

            $where = $this->project->getAdapter()->quoteInto('idprojeto = ?', (int) $this->_request->getPost('id'));

            $this->project->update($data,$where);

            $this->_redirect('projeto/detalhesgerente/idProj/'.$id_proj.'/flag/2');
        }
    }
    
    
    /**
     * Funcao responsavel pela delecao de um projeto existente
     * 
     * @access public 
     * @return void
     * 
     */
    public function deleteAction()
    {
        //Precisa so colocar as flags informando o ocorrido para o usuario
        $id_proj = $this->_getParam('id');
       
        $select = $this->project->select();
        $select -> from($this->project, 'COUNT(*) AS num')
                -> where('idprojeto = ?', $id_proj)
                ->where('estado_idestado = 7');
        
        $selectcolab = $this->colab->select();
        $selectcolab -> from($this->colab, 'COUNT(*) AS num') 
                     -> where('projeto_idprojeto = ?', $id_proj);

      if($this->colab->fetchRow($selectcolab)->num == 0)
      {  
        if($this->project->fetchRow($select)->num != 0)
        {
            $where = $this->projbugzilla->getAdapter()->quoteInto('projeto_idprojeto = ?',$id_proj);
            $this->projbugzilla->delete($where);

            $where = $this->projgit->getAdapter()->quoteInto('projeto_idprojeto = ?',$id_proj);
            $this->projgit->delete($where);

            $where = $this->project->getAdapter()->quoteInto('idprojeto = ?',$id_proj);
            $this->project->delete($where);

            $this->_redirect('projeto/index/flag/4');
        }
        else
        {
            $this->_redirect('projeto/detalhesgerente/flag/1/idProj/'.$id_proj);
        }
      }
        else
        {
            $this->_redirect('projeto/detalhesgerente/flag/3/idProj/'.$id_proj);
        }
    }
    
    
    /**
     * Funcao responsavel pela exibicao detalhada de um projeto
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
     * Funcao responsavel pela exibicao de todos os projetos daquela filial
     * para o administrador responsavel pela filial.
     * 
     * @access public 
     * @return void
     * 
     */
    public function adminfilialAction(){

        //Verifica permissão
        if(!$this->adminFilial){
            $this->_redirect('/index/negado');
        }
        
        $idFuncLogado = $this->funcLogado->idfuncionario;
        
        $selecFilial = $this->filial->select()
                ->where('responsavel = ?', $idFuncLogado);
        $filialEncontrada = $this->filial->fetchRow($selecFilial);
        $this->view->estadoProj = $this->estadoProj;
        if($filialEncontrada->responsavel == $idFuncLogado){
            $this->view->nomeFilial = $filialEncontrada->nome;
            $selectProjs = $this->projFiliais->select()
                    ->where('nomeFilial = ?', $filialEncontrada->nome);
            
            $rows = $this->projFiliais->fetchAll($selectProjs);
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
     * Funcao responsavel pela exibicao de todos os projetos daquela empresa
     * para o administrador geral da empresa.
     * 
     * @access public 
     * @return void
     * 
     */
    public function admingeralAction(){

        //Verifica permissão
        if(!$this->adminEmpresa){
            $this->_redirect('/index/negado');
        }
        
        $idFuncLogado = $this->funcLogado->idfuncionario;
                
        $selecEmpresa = $this->empresa->select()
                ->where('responsavelGeral = ?', $idFuncLogado);
        $empresaEncontrada = $this->empresa->fetchRow($selecEmpresa);
        $this->view->estadoProj = $this->estadoProj;
        if($empresaEncontrada->responsavelGeral == $idFuncLogado){
            $this->view->nomeEmpresa = $empresaEncontrada->nome;
            $selectProjs = $this->project->select();

            $rows = $this->project->fetchAll($selectProjs);

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
     * 
     */
    private function inverte_data($data, $separador)
    {
        $nova_data = implode("".$separador."",array_reverse(explode("".$separador."",$data)));
                        return $nova_data;
    }
    
    /**
     * Funcao responsavel pela busca de detalhes de um dado projeto e exibe para
     * o seu respectivo gerente. Caso contrario as informacoes sao negadas
     * 
     * @access public 
     * @return void
     * 
     */
    public function detalhesgerenteAction(){
        $this->view->flag = $this->_request->getParam('flag');
        $idFuncLogado = $this->funcLogado->idfuncionario;
        $this->view->idFuncLogado = $idFuncLogado;
        $this->view->estadoProj = $this->estadoProj;
        $idProj = $this->_getParam('idProj');
        $where = $this->project->getAdapter()->quoteInto('idprojeto = ?', $idProj);
        $selecProj = $this->project->select()
                ->where($where);
        
        $projetoEncontrado = $this->project->fetchRow($selecProj);
        if($projetoEncontrado->idGerente == $idFuncLogado){
            $this->view->projetoEncontrado = $projetoEncontrado;
            
            
            $nomeProj = $projetoEncontrado->nome;
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
            
        }else{
            $this->_redirect('/projeto/index');
        }
    }
    
    /**
     * Funcao responsavel pela busca de detalhes de um dado projeto e exibe para
     * o seu respectivo administrador da filial.
     * Caso contrario as informacoes sao negadas
     * 
     * @access public 
     * @return void
     * 
     */
    public function detalhesadminfilialAction(){
        
        
        $idFuncLogado = $this->funcLogado->idfuncionario;
        $this->view->idFuncLogado = $idFuncLogado;
        $this->view->estadoProj = $this->estadoProj;
        $idProj = $this->_getParam('idProj');
        $where = $this->project->getAdapter()->quoteInto('idprojeto = ?', $idProj);
        $selecProj = $this->project->select()
                ->where($where);
        
        $projetoEncontrado = $this->project->fetchRow($selecProj);
        if($this->adminFilial){
            $this->view->projetoEncontrado = $projetoEncontrado;
            
            
            $nomeProj = $projetoEncontrado->nome;
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
            
        }else{
            $this->_redirect('/projeto/adminfilial');
        }
    }
    
    /**
     * Funcao responsavel pela busca de detalhes de um dado projeto e exibe para
     * o seu respectivo administrador da geral da empresa.
     * Caso contrario as informacoes sao negadas
     * 
     * @access public 
     * @return void
     * 
     */
     public function detalhesadmingeralAction(){
        
        
        $idFuncLogado = $this->funcLogado->idfuncionario;
        $this->view->idFuncLogado = $idFuncLogado;
        $this->view->estadoProj = $this->estadoProj;
        $idProj = $this->_getParam('idProj');
        $where = $this->project->getAdapter()->quoteInto('idprojeto = ?', $idProj);
        $selecProj = $this->project->select()
                ->where($where);
        
        $projetoEncontrado = $this->project->fetchRow($selecProj);
        if($this->adminEmpresa){
            $this->view->projetoEncontrado = $projetoEncontrado;
            
            
            $nomeProj = $projetoEncontrado->nome;
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
            
        }else{
            $this->_redirect('/projeto/index');
        }
    }
}







