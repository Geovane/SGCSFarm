<?php
class ColaboradorController extends Zend_Controller_Action
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
        
        /**
        * Variáveis responsáveis pelo acesso as tabelas do banco de dados.
        * 
        * @name project
        * @name funcionario
        * @name estado
        * @name projbugzilla
        * @name projgit
        * @name funcaoproj
        * @name colab
         * @name empresafilial
         * @name funcaoColabProj
         * @name tipoEstadoTarefaColabProj
         * @name colabProj
         * @name ProjGerFiliColab
         * @name empresa
         * @name filial
        * @access disponível em todos os actions do controller Tarefas
        */
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
        $this->funcfilial = new Model_DbTable_FuncFilial();
        $this->tarefa = new Model_DbTable_TarColabProj();
    }
    
    /**
     * Funcao inicial do controller colaboradores.
     * envia ao seu view as informacoes sobre os colaboradores.
     * 
     * @access public 
     * @return void
     * 
     */
    public function indexAction()
    {

        //Manda a flag de mensagens pra view index
        $this->view->flag = $this->_request->getParam('flag');

        $this->verificaGerente();
        
        $id_proj = $this->_getParam('id');
        
        $select = $this->ProjGerFiliColab->select();
        $select -> where('idprojeto = ?', $id_proj)
                -> where("funcaoColaborador != 'Gerente' ");
        
        $selectproj = $this->project->select();
        $selectproj -> where('idprojeto = ?', $id_proj);
        
        $rows = $this->ProjGerFiliColab->fetchAll($select);
        
        $paginator = Zend_Paginator::factory($rows);
        //Passa o numero de registros por pagina
        $paginator->setItemCountPerPage(8);

        $this->view->paginator = $paginator;
        $this->view->projeto = $this->project->fetchAll($selectproj);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    
    /**
     * Funcao responsavel pela insercao ao banco dos dados de uma novo colaborador
     * 
     * @access public 
     * @return void
     * 
     */
    public function createAction()
    {
        //Verifica se um usuário é gerente do projeto que deseja adcionar colaboradores
        $this->verificaGerente();
        
        $id_proj = $this->_getParam('id');
        
        /* Efetua uma busca no banco para obter o projeto no qual se deseja inserir um colaborador
         * 
         * @name $select
         */
        $select = $this->project->select();
        $select -> where('idprojeto = ?', $id_proj);
        
        /*Variável que guarda o id do gerente
         * 
         * @name $idGerente
         */ 
        $gerente = $this->project->fetchRow($select)->idGerente;
        
        //Obtenção da filial do gerente
        $selectfilial = $this->funcfilial->select();
        $selectfilial -> where('idfuncionario = ?',$gerente);
        
        $filial = $this->funcfilial->fetchRow($selectfilial)->nomeEmpresa;
        
        /* Busca de todos os usuários da filial menos o gerente para exibição na view create 
         * 
         * @name $funcionario
         */
        $funcionario = $this->funcfilial->select();
        $funcionario -> where('nomeEmpresa = ?', $filial)
                     -> where('idfuncionario != ?',$gerente);
                     
        /*Busca de todas as funções possíveis no banco, exceto a função de gerente
         * 
         * @name $funcao
         */
        $funcao = $this->funcaoproj->select();
        $funcao -> where('idfuncaoProjeto != 40');
        
        //Envio das buscas para a view create
        $this->view->projeto = $this->project->fetchAll($select);
        $this->view->funcionario = $this->funcfilial->fetchAll($funcionario);
        $this->view->funcao = $this->funcaoproj->fetchAll($funcao);
        
        if($this->_request->isPost())
        {
            /* Variável responsável por armazenar todos os dados que serão inseridos no banco de dados
             * 
             * @name $data
             */
            $data = array
            (
                'projeto_idprojeto' => $this->_request->getPost('id'),
                'funcionario_idfuncionario' => $this->_request->getPost('funcionario'),
                'dedicacaoMes' => $this->_request->getPost('horas'),
                'funcaoProjeto_idfuncaoProjeto' => $this->_request->getPost('funcao')
            );
            
            /* Busca do idcolaborador do funcionário que se deseja inserir a fim de saber se ele já possue uma função.
             * Caso o usuário já seja um colaborador ele não pode ser inserido na no banco.
             * 
             * @name $idcolaborador
             */
            $idcolaborador = $this->colab->select();
            $idcolaborador -> where('funcionario_idfuncionario = ?',$data['funcionario_idfuncionario'])
                           -> where('projeto_idprojeto = ?',$data['projeto_idprojeto']) ;
            
            /* Variável que armazena o id do funcionário encontrado na tabela colaboradores
             * 
             * @name $colaborador
             */
            $colaborador = $this->colab->fetchRow($idcolaborador)->funcionario_idfuncionario;
            
            if($colaborador != 0)
            {
                $this->view->mensagem = "<div id='alerta'>Um colaborador não pode exercer mais de uma função no projeto</div>";
            }
            else
            {    
            $this->colab->insert($data);
            
            $this->_redirect('colaborador/index/id/'.$id_proj.'/flag/2');
            }
        }
    }
    
    /**
     * Funcao responsavel pela edicao de um colaborador existente 
     * 
     * @access public 
     * @return void
     * 
     */
    public function editAction()
    {

        
        if($this->_request->isPost())
        {
            /* Variável responsável por armazenar todos os dados que serão editados no banco de dados
             * 
             * @name $data
             */
            $data = array
            (
             'projeto_idprojeto' =>$this->_request->getPost('idProj') ,
             'funcionario_idfuncionario' => $this->_request->getPost('idFunc'),
             'dedicacaoMes' => $this->_request->getPost('horas'),
             'funcaoProjeto_idfuncaoProjeto' => $this->_request->getPost('funcao')
            );


            /* Varíavel que busca o idcolaborador do funcionário que se dejesa editar os dados na tabela
             * 
             * @name $colaborador
             */
            $colaborador = $this->colab->select();
            $colaborador -> where('funcionario_idfuncionario = ?',$this->_request->getPost('idFunc'))
                         -> where('projeto_idprojeto = ?',$this->_request->getPost('idProj'));
            
            /* Variável responsável por armazenar o idcolaboradores encontrado pela busca
             * 
             * @name $id_colab
             */
            $id_colab = $this->colab->fetchRow($colaborador)->idcolaboradores;

            /* Variável responsável por determinar em qual colaborador será feita a edição dos dados
             * 
             * @name $where
             */
            $where = $this->colab->getAdapter()->quoteInto('idcolaboradores = ?', (int) $id_colab);
            
            $this->colab->update($data,$where);
            
            $this->_redirect('/colaborador/index/id/'.$this->_request->getPost('idProj').'/flag/1');

        }else{
                //Verifica se o usuário é gerente do projeto que se deseja editar os colaboradores
                $this->verificaGerente();
                   
                /* Variáveis responsáveis por pegar o id do funcionario e do projeto
                 * 
                 * @name $id_funca
                 * @name $id_proj
                 */
                $id_func = $this->_getParam('idfunc');
                $id_proj = $this->_getParam('id');

                /* Busca de todos os usuários da filial menos o gerente para exibição na view create 
                 * 
                 * @name $funcionario
                 */
                $funcionario = $this->ProjGerFiliColab->select();
                $funcionario -> where('idFuncionarioColaborador = ?',$id_func)
                             -> where('idprojeto = ?',$id_proj);
                
                 /*Busca de todas as funções possíveis no banco, exceto a função de gerente
                  * 
                  * @name $funcao
                  */
                $funcao = $this->funcaoproj->select();
                $funcao -> where('idfuncaoProjeto != 40');

                $this->view->funcionario = $this->ProjGerFiliColab->fetchAll($funcionario);
                $this->view->funcao = $this->funcaoproj->fetchAll($funcao);

        }
    }
    
    /**
     * Funcao responsavel pela delecao de um colaborador existente
     * 
     * @access public 
     * @return void
     * 
     */
     public function deleteAction()
        {
            //Verifica se o usuário tem permissão para deletar um colaborador
            $this->verificaGerente();
            
            /* Variáveis responsáveis por pegar o id do funcionario e do projeto
             * 
             * @name $id_funca
             * @name $id_proj
            */
            $id_func = $this->_getParam('idfunc');
            $id_proj = $this->_getParam('id');
            
            /* Varíavel que busca o idcolaborador do funcionário que se dejesa excluir 
             * 
             * @name $colaborador
             */
            $colaborador = $this->colab->select();
            $colaborador -> where('funcionario_idfuncionario = ?',$id_func)
                         -> where('projeto_idprojeto = ?',$id_proj);
            
            /* Variável responsável por armazenar o idcolaboradores encontrado pela busca
             * 
             * @name $id_colab
             */
            $id_colab = $this->colab->fetchRow($colaborador)->idcolaboradores;
            
            /* Variável que busca se o colaborador que se deseja excluir possui tarefas assosciadas a ele
             * 
             * @name $tarefa
             */
            $tarefa = $this->tarefa->select();
            $tarefa -> where('idColab = ?',$id_colab);
            
            /* Variável que armazena o id do colaborador encontrado, ou não na tabela tarefas,se
             * algum colaborador for encontrado, não é possível remove-lo do banco até que ele não
             * tenha mais nenhuma tarefa associada a ele
             * 
             * @name id_colab_tarefa
             */
            $id_colab_tarefa = $this->tarefa->fetchRow($tarefa)->idColab;
            
            if($id_colab == $id_colab_tarefa)
            {
               $this->_redirect('colaborador/index/id/'.$id_proj.'/flag/5');   
            }
            
            /* Varíavel que armazena o id do colaborador que se dejesa excluir
             * 
             * @name $where
             */
            $where = $this->colab->getAdapter()->quoteInto('idcolaboradores = ?', (int) $id_colab);
            
            $this->colab->delete($where);
            
            $this->_redirect('colaborador/index/id/'.$id_proj.'/flag/4');
            
            
        }
    
    /* Função que verifica se o usuário é gerente do projeto e lhe concede permissão para gerenciar os colaboradores
     * 
     * @access private
     * @return void
     */    
    private function verificaGerente()
    {
        /* Variáveis responsáveis por pegar o id do projeto e do funcionario logado
         * 
         * @name $id_proj
         * @name $idFunc
        */
        $idFunc = $this->funcLogado->idfuncionario;
        $id_proj = $this->_getParam('id');
        
        /* Varíavel que procura na tabela de projetos se o usuário logado é gerente do projeto,
         * se não for ele redirecionado para a página principal de projetos com uma mensagem de erro
         * 
         * @name $select
         * 
         */
        $select = $this->project->select();
        $select ->from($this->project,array('idGerente'))
                -> where('idprojeto = ?', $id_proj);
        
        /* Variável que armazena o id encontrado por essa busca e é usada posteriormente na verificação,
         * para saber se o usuário logado é gerente do projeto
         * 
         * @name $resultado
         */
        $resultado = $this->project->fetchRow($select)->idGerente;
        
        if($idFunc == $resultado)
        {
            return;
        }else
            $this->_redirect('/projeto/index/flag/1');
    }
    
}
?>