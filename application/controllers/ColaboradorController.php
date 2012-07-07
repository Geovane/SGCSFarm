<?php
class ColaboradorController extends Zend_Controller_Action
{
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
        $idFunc = $this->funcLogado->idfuncionario;
        $idEmpresa = $dadosIndex[0]->empresa_idempresa;
        $idFilial = $this->funcLogado->empresaFilial_idempresaFilial;

        //Informações relativas a permissoes (Se tiver permissão retorna True)
        $adminFilial = Model_Permissoes::responsavelFilial($idFunc,$idFilial);
        $adminEmpresa = Model_Permissoes::responsavelEmpresa($idFunc,$idEmpresa);
        $this->view->AdminFilial = $adminFilial;
        $this->view->AdminEmpresa = $adminEmpresa;


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
    }
    
    public function indexAction()
    {
        $this->verificaGerente();
        
        $id_proj = $this->_getParam('id');
        
        $select = $this->ProjGerFiliColab->select();
        $select -> where('idprojeto = ?', $id_proj)
                -> where("funcaoColaborador != 'Gerente' ");
        
        $rows = $this->ProjGerFiliColab->fetchAll($select);
        
        $paginator = Zend_Paginator::factory($rows);
        //Passa o numero de registros por pagina
        $paginator->setItemCountPerPage(8);

        $this->view->paginator = $paginator;
        $this->view->projeto = $this->ProjGerFiliColab->fetchAll($select);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    
    public function createAction()
    {
        $this->verificaGerente();
        
        $id_proj = $this->_getParam('id');
        
        $select = $this->ProjGerFiliColab->select();
        $select -> where('idprojeto = ?', $id_proj);
        
        $filial = $this->ProjGerFiliColab->fetchRow($select)->nomeFilialProj;
        $gerente = $this->ProjGerFiliColab->fetchRow($select)->nomeGerente;
        
        
        
        $funcionario = $this->funcfilial->select();
        $funcionario -> where('nomeEmpresa = ?', $filial)
                     -> where('nome != ?',$gerente);
                     
        
        $funcao = $this->funcaoproj->select();
        $funcao -> where('idfuncaoProjeto != 40');
        
        $this->view->ProjGerFiliColab = $this->ProjGerFiliColab->fetchAll($select);
        $this->view->funcionario = $this->funcfilial->fetchAll($funcionario);
        $this->view->funcao = $this->funcaoproj->fetchAll($funcao);
        
        if($this->_request->isPost())
        {
            $data = array
            (
                'projeto_idprojeto' => $this->_request->getPost('id'),
                'funcionario_idfuncionario' => $this->_request->getPost('funcionario'),
                'dedicacaoMes' => $this->_request->getPost('horas'),
                'funcaoProjeto_idfuncaoProjeto' => $this->_request->getPost('funcao')
            );
            
            $idcolaborador = $this->colab->select();
            $idcolaborador -> where('funcionario_idfuncionario = ?',$data['funcionario_idfuncionario'])
                           -> where('projeto_idprojeto = ?',$data['projeto_idprojeto']) ;
            
            $colaborador = $this->colab->fetchRow($idcolaborador)->funcionario_idfuncionario;
            
            if($colaborador != 0)
            {
             $this->_redirect('colaborador/index/id/'.$id_proj);   
            }    
            $this->colab->insert($data);
            
            $this->_redirect('projeto/detalhes/idProj/'.$id_proj);
        }
    }
    
    public function editAction()
    {
        $this->verificaGerente();
        
        $id_func = $this->_getParam('idfunc');
        $id_proj = $this->_getParam('id');
        
        $funcionario = $this->ProjGerFiliColab->select();
        $funcionario -> where('idFuncionarioColaborador = ?',$id_func)
                     -> where('idprojeto = ?',$id_proj);
        
        $funcao = $this->funcaoproj->select();
        $funcao -> where('idfuncaoProjeto != 40');
       
        $this->view->funcionario = $this->ProjGerFiliColab->fetchAll($funcionario);
        $this->view->funcao = $this->funcaoproj->fetchAll($funcao);
        
        //PRECISA SER REVISADO, PROVAVELMENTE O ERRO NÃO SEJA NO CÓDIGO MAS SIM NO BANCO
        if($this->_request->isPost())
        {
            $data = array
            (
             'projeto_idprojeto' => $id_proj,
             'funcionario_idfuncionario' => $id_func,
             'dedicacaoMes' => $this->_request->getPost('horas'),
             'funcaoProjeto_idfuncaoProjeto' => $this->_request->getPost('funcao')   
            );
            
            $colaborador = $this->colab->select();
            $colaborador -> where('funcionario_idfuncionario = ?',$id_func)
                         -> where('projeto_idprojeto = ?',$id_proj);
            
            $id_colab = $this->colab->fetchRow($colaborador)->idcolaboradores;
            
            $where = $this->colab->getAdapter()->quoteInto('idcolaboradores = ?', (int) $id_colab);
            
            $this->colab->update($data,$where);
            
            $this->_redirect('projeto/detalhes/idProj/'.$id_proj);
        }    
    }
    
     public function deleteAction()
        {
            $this->verificaGerente();
            
            $id_func = $this->_getParam('idfunc');
            $id_proj = $this->_getParam('id');
            
            $colaborador = $this->colab->select();
            $colaborador -> where('funcionario_idfuncionario = ?',$id_func)
                         -> where('projeto_idprojeto = ?',$id_proj);
            
            $id_colab = $this->colab->fetchRow($colaborador)->idcolaboradores;
            
            $where = $this->colab->getAdapter()->quoteInto('idcolaboradores = ?', (int) $id_colab);
            
            $this->colab->delete($where);
            
            $this->_redirect('projeto/detalhes/idProj/'.$id_proj);
            
            
        }   
    private function verificaGerente()
    {
        $idFunc = $this->funcLogado->idfuncionario;
        $id_proj = $this->_getParam('id');
        
        $select = $this->ProjGerFiliColab->select();
        $select ->from($this->ProjGerFiliColab,array('idGerente'))
                -> where('idprojeto = ?', $id_proj);
        
        $resultado = $this->ProjGerFiliColab->fetchRow($select)->idGerente;
        
        if($idFunc == $resultado)
        {
            return;
        }else
            $this->_redirect('projeto/index');
        
        
    }
    
}
?>