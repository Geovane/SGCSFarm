<?php

class IndexController extends Zend_Controller_Action
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

        $this->tarefas = new Model_DbTable_TipoEstadoTarefaColabProj();
    }

    public function indexAction()
    {
        $idFuncLogado = $this->funcLogado->idfuncionario;
        
        $selectTarefa = $this->tarefas->select()
                    ->where('idfuncionario = ?', $idFuncLogado);
        
        @$rows = $this->tarefas->fetchAll($selectTarefa);
        if($rows == null){
            $this->view->exiteTarefas = '0';
        }else{
            $this->view->exiteTarefas = '1';
        }
        $this->view->todasTarefas = $rows;
        
    }

    public function negadoAction(){ }


}



