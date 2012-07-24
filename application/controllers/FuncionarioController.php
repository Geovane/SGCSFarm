<?php
/**
 * Classe responsavel pelo gerenciamento da criação, edição, exibição e deleção das
 * de funcionários no sistema, separando o acesso de funcionarios de acordo com suas filiais e
 * e dando acesso a todos eles para o responsavel pela empresa.
 * Alem disso, mantem as restriçoes de integridade do sistema relativa as demais entidades
 * do sistema.
 * 
 * @author Geovane Mimoso
 * @version 0.1
 * @access public
 * 
 */

class FuncionarioController extends Zend_Controller_Action
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

      
        /* Initialize action controller here */
        $this->funcionario = new Model_DbTable_Func();
        $this->filial = new Model_DbTable_Filial();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->projeto = new Model_DbTable_Proj();
        $this->userGit = new Model_DbTable_UserGit();
        $this->userBug = new Model_DbTable_UserBugzilla();
        $this->redefinir = new Model_DbTable_RedefinirSenha();

    }

     /**
     *
     * Metodo que envia as informações daos funcionarios da filial do responsavel logado para serem exibidas na view index
     * Pode ser acessado apenas pelo responsavel da filial
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function indexAction()
    {
        //Verifica permissão
        if(!$this->adminFilial){
            $this->_redirect('/index/negado');
        }


        $page = $this->_request->getParam('page');

            if ( isset($page) ){
                $this->view->mem = true;
            }
        
         $this->view->flag = $this->_request->getParam('flag');
         $select = $this->funcionario->select();
         $select->where('empresaFilial_idempresaFilial = ?', $this->idFilial);
         $select->order('nome');
         $rows = $this->funcionario->fetchAll($select);

         //Cria a paginação relativa a exibição dos funcionarios

         $paginator = Zend_Paginator::factory($rows);
         //Passa o numero de registros por pagina
         $paginator->setItemCountPerPage(10);
      
         $this->view->paginator = $paginator;
         $paginator->setCurrentPageNumber($this->_getParam('page'));

    }



    /**
     *
     * Metodo que insere os funcionarios na filial do responsavel logado no sistema,
     * Alem disso deve manter as restrições de integridade dos seus dados e tem seu acesso restrito
     * ao responsavel pela filial.
     *
     * @author Geovane mimoso
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

        $this->view->flag = $this->_request->getParam('flag');
        
         if ( $this->_request->isPost() )
            {

             if(!$this->funcionario->existeDoc($this->_request->getPost('doc')))
             {
                 if(!$this->funcionario->existeLogin($this->_request->getPost('login')))
                 {
                    $data = array(
                        'nome'  => $this->_request->getPost('nome'),
                        'documentoIdentificacao'  => $this->_request->getPost('doc'),
                        'login' => $this->_request->getPost('login'),
                        'senha'  => sha1($this->_request->getPost('doc')),
                        'email'  => $this->_request->getPost('email'),
                        'empresaFilial_idempresaFilial' => $this->idFilial,
                        'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
                    );

                    //print_r($data);

                    //Insere funcionario e guardo o id dele na variavel $idInserido
                    $idInserido = $this->funcionario->insert($data);

                    //Cria usuario Git e bugZilla
                    $data1 = array(
                        'funcionario_idfuncionario'  => $idInserido,
                        'usuario'  => $this->_request->getPost('login'),
                        'senha'  => sha1($this->_request->getPost('doc'))
                    );

                     //print_r($data1);
                   //Insere usuario Git e bugZilla
                   $this->userBug->insert($data1);
                   $this->userGit->insert($data1);

                   $this->_redirect('funcionario/index/flag/1');

                 }else{
                   $this->_redirect('funcionario/create/flag/2');
                 }
             }else{

                  $this->_redirect('funcionario/create/flag/1');
             }

            }
    }

   /**
     *
     * Metodo que edita os funcionarios das filiais.
     * Apenas o responsavel pela filial tem acesso.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function editAction(){

        //Verifica permissão
        if(!$this->adminFilial){
            $this->_redirect('/index/negado');
        }

       $func_id = $this->_getParam('id');

       $result  = $this->funcionario->find($func_id);
       $this->view->funcionario = $result->current();

            if ( $this->_request->isPost() )
            {

                $data = array(
                    'nome'  => $this->_request->getPost('nome'),
                    'email'  => $this->_request->getPost('email'),
                );

                $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('id'));

                $this->funcionario->update($data, $where);

                $this->_redirect('/funcionario/index/flag/2');
            }


    }

    /**
     *
     * Metodo que deleta os funcionarios do sistema,
     * verificando porem as dependencias para essa deleção,
     * caso essas dependências existam essa deleção não é realizada,
     * Caso a deleção se confirme, os registros das tabelas relativas a esse funcionario tambem são deletados.
     * Tanto o responsavel pela filial tanto o responsavel pela empresa tem acesso
     * aos seus respectivos funcionários.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function deleteAction(){

         //Verifica permissão
        if(!($this->adminFilial || $this->adminEmpresa) ){
            $this->_redirect('/index/negado');
        }

        $func_id = $this->_getParam('id');
        $tipo = $this->_getParam('tipo');

            //Verifica se o funcionario esta inserido como colaborador em algum projeto
            $select = $this->colaboradores->select();
            $select->from($this->colaboradores, 'COUNT(*) AS num');
            $select->where('funcionario_idfuncionario = ?', $func_id);

            //Verifica se o funcionario é gerente de algum projeto
            $selectP = $this->projeto->select();
            $selectP->from($this->projeto, 'COUNT(*) AS num');
            $selectP->where('idGerente = ?', $func_id);

            //Verifica se o funcionario é responsavel por alguma filial
            $selectF = $this->filial->select();
            $selectF->from($this->filial, 'COUNT(*) AS num');
            $selectF->where('responsavel = ?', $func_id);



            if ( $this->colaboradores->fetchRow($select)->num == 0 && $this->projeto->fetchRow($selectP)->num == 0 && $this->filial->fetchRow($selectF)->num == 0 )
            {
                //Chama metodo que deleta os registros de outras tabelas referentes ao funcionario
                $this->deletarRegistrosUsuario($func_id);

                //deleta o funcionario
                $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', $func_id);
                $this->funcionario->delete($where);

                if($tipo!=2)
                $this->_redirect('funcionario/index/flag/3');

                $this->_redirect('funcionario/indexemp/flag/3');

            } else
            {
              if($tipo!=2)
              $this->_redirect('funcionario/index/flag/4');

              $this->_redirect('funcionario/indexemp/flag/4');
            }

    }


   /**
     *
     * Método que deleta do banco todos os registros que envolvem o usuário deletado
     *
     *
     * @author Geovane mimoso
     * @access private
     * @param int $func_id id do funcionario a ser deletado.
     * @return boolean
     *
     */
    private function deletarRegistrosUsuario($func_id)
    {
        if($this->redefinir->existeRedefinir($func_id)){
        //deleta todas as solicitaçãoes de redefinição de senha
        $where = $this->redefinir->getAdapter()->quoteInto('funcionario_idfuncionario = ?', $func_id);
        $this->redefinir->delete($where);
        }

        if($this->userGit->existeUserGit($func_id)){
        //deleta o usuario git relacionado a esse funcionario
        $where = $this->userGit->getAdapter()->quoteInto('funcionario_idfuncionario = ?', $func_id);
        $this->userGit->delete($where);
        }

        if($this->userBug->existeUserBug($func_id)){
        //deleta o usuario bugZilla relacionado a esse funcionario
        $where = $this->userBug->getAdapter()->quoteInto('funcionario_idfuncionario = ?', $func_id);
        $this->userBug->delete($where);
        }

        return true;
    }

     /**
     *
     * Metodo que envia as informações de todos os funcionarios da empresa para serem exibidas na view indexemp
     * Pode ser acessado apenas pelo responsavel pela empresa
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
        public function indexempAction()
        {

        //Verifica permissão
        if(!$this->adminEmpresa){
            $this->_redirect('/index/negado');
        }

        $page = $this->_request->getParam('page');

            if ( isset($page) ){
                $this->view->mem = true;
            }

         $this->view->flag = $this->_request->getParam('flag');
         $select = $this->funcionario->select()->order('nome');

         $rows = $this->funcionario->fetchAll($select);

         //Cria a paginação relativa a exibição dos funcionarios

         $paginator = Zend_Paginator::factory($rows);
         //Passa o numero de registros por pagina
         $paginator->setItemCountPerPage(10);

         $this->view->paginator = $paginator;
         $paginator->setCurrentPageNumber($this->_getParam('page'));

    }

    /**
     *
     * Metodo que insere os funcionarios na empresa,
     * Alem disso deve manter as restrições de integridade dos seus dados e tem seu acesso restrito
     * ao responsavel pela empresa.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function createempAction()
    {
        //Verifica permissão
        if(!$this->adminEmpresa){
            $this->_redirect('/index/negado');
        }

        $this->view->flag = $this->_request->getParam('flag');

         if ( $this->_request->isPost() )
            {

             if(!$this->funcionario->existeDoc($this->_request->getPost('doc')))
             {
                 if(!$this->funcionario->existeLogin($this->_request->getPost('login')))
                 {
                    $data = array(
                        'nome'  => $this->_request->getPost('nome'),
                        'documentoIdentificacao'  => $this->_request->getPost('doc'),
                        'login' => $this->_request->getPost('login'),
                        'senha'  => sha1($this->_request->getPost('doc')),
                        'email'  => $this->_request->getPost('email'),
                        'empresaFilial_idempresaFilial' => (int)0,
                        'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
                    );

                    //print_r($data);

                    //Insere funcionario e guardo o id dele na variavel $idInserido
                    $idInserido = $this->funcionario->insert($data);

                    //Cria usuario Git e bugZilla
                    $data1 = array(
                        'funcionario_idfuncionario'  => $idInserido,
                        'usuario'  => $this->_request->getPost('login'),
                        'senha'  => sha1($this->_request->getPost('doc'))
                    );

                     //print_r($data1);
                   //Insere usuario Git e bugZilla
                   $this->userBug->insert($data1);
                   $this->userGit->insert($data1);

                   $this->_redirect('funcionario/indexemp/flag/1');

                 }else{
                   $this->_redirect('funcionario/createemp/flag/2');
                 }
             }else{

                  $this->_redirect('funcionario/createemp/flag/1');
             }

            }
    }

    /**
     *
     * Metodo que edita os funcionarios da empresa.
     * Apenas o responsavel pela empresa tem acesso.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function editempAction(){

        //Verifica permissão
        if(!$this->adminEmpresa){
            $this->_redirect('/index/negado');
        }

       $func_id = $this->_getParam('id');

       $result  = $this->funcionario->find($func_id);
       $this->view->funcionario = $result->current();
       $this->view->filial = $this->filial->fetchAll();

            if ( $this->_request->isPost() )
            {
            //Guarda o id do fuincionario e da filial dele, enviados via post
            $id = $this->_request->getPost('id');
            $idfilial = $this->_request->getPost('id_filial');

            if($idfilial == $this->_request->getPost('idFilial') )
            {
                    $data = array(
                            'nome'  => $this->_request->getPost('nome'),
                            'email'  => $this->_request->getPost('email')
                        );

                    $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('id'));
                    $this->funcionario->update($data, $where);
                    $this->_redirect('funcionario/indexemp/flag/2');


            }
            else{

                    //Verifica se o funcionario esta inserido como colaborador em algum projeto
                    $select = $this->colaboradores->select();
                    $select->from($this->colaboradores, 'COUNT(*) AS num');
                    $select->where('funcionario_idfuncionario = ?', $id);

                    //Verifica se o funcionario é gerente de algum projeto
                    $selectP = $this->projeto->select();
                    $selectP->from($this->projeto, 'COUNT(*) AS num');
                    $selectP->where('idGerente = ?', $id);

                    //Verifica se o funcionario é responsavel por alguma filial
                    $selectF = $this->filial->select();
                    $selectF->from($this->filial, 'COUNT(*) AS num');
                    $selectF->where('responsavel = ?', $func_id);


                    if ( $this->colaboradores->fetchRow($select)->num == 0 && $this->projeto->fetchRow($selectP)->num == 0 && $this->filial->fetchRow($selectF)->num == 0)
                    {
                        $data = array(
                            'nome'  => $this->_request->getPost('nome'),
                            'email'  => $this->_request->getPost('email'),
                            'empresaFilial_idempresaFilial' => $this->_request->getPost('idFilial')
                        );

                        $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('id'));
                        $this->funcionario->update($data, $where);
                        $this->_redirect('funcionario/indexemp/flag/5');
                    }
                    else{

                        $this->_redirect('funcionario/indexemp/flag/4');
                    }
          }

        }
    }
}