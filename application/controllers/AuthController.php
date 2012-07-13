<?php

/**
 *
 * Classe responsavel pelo sistema de autenticação do SGCSFarm entre suas
 * atribuições estão realizar autenticação do funcionario, gerenciar e criar a sessão de usuario
 * e alem de redefinir senha de usuario em seu primeiro acesso;
 *
 * @author Geovane mimoso
 * @version 0.1
 * @access public
 *
 */

class AuthController extends Zend_Controller_Action
{
    private $_usuario;

     /**
     * Funcao que inicializa todos os parametros necessarios para o correto
     * funcionamento dos actions, como conexões com o banco de dados e
     * variaveis de controle dos actions. 
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function init()
    {
        $this->_usuario = new Model_DbTable_Func();

        // Seta layout de Login
        $this->_helper->layout->setLayout('login');

    }

    /**
     * Redireciona a raiz do sistema para o action responsavel pela a autenticação;
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function indexAction()
    {
        return $this->_helper->redirector('login');
    }

     /**
     * Recebe os dados de autenticação do usuario via post, valida e cria a sessão com os dados
     * predefinidos tambem nesta action, caso seja o primeiro acesso do usuario redireciona ele
     * para o action responsavel pela redefinição de senha.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function loginAction()
    {
        //Verifica se existem dados de POST
        if ( $this->getRequest()->isPost() )
        {
            $data = array(
                'login'         => stripslashes(trim($this->getRequest()->getPost('login'))),
                'senha'         => stripslashes(trim($this->getRequest()->getPost('senha')))
            );

            //Verifica se todos os campos do formulário foram preenchidos.
            if ( !empty($data['login']) &&
                 !empty($data['senha']) )
            {

                $login = $data['login'];
                $senha = $data['senha'];

                $dbAdapter = Zend_Db_Table::getDefaultAdapter();

                //Inicia o adaptador Zend_Auth para banco de dados
                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                $authAdapter->setTableName('funcionario')
                            ->setIdentityColumn('login')
                            ->setCredentialColumn('senha')
                            ->setCredentialTreatment('SHA1(?)');

                //Define os dados para processar o login
                $authAdapter->setIdentity($login)
                            ->setCredential($senha);

                //Efetua o login
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                //Verifica se o login foi efetuado com sucesso
                if ( $result->isValid() ) {

                    //Armazena os dados do usuário em sessão
                    $info = $authAdapter->getResultRowObject(array(
                                                                'idfuncionario',
                                                                'nome',
                                                                'empresaFilial_idempresaFilial'
                                                             )
                            );

                    $storage = $auth->getStorage();
                    $storage->write($info);

                    /*Verifica se é o primeiro login.
                     * Se for, redireciona para o usuário fazer a alteração da senha
                     * Senão, vai para tela principal do sistema
                     * */


                    $usuario = Zend_Auth::getInstance()->getIdentity();

                    $result  = $this->_usuario->find($usuario->idfuncionario);
                    $dados = $result->current();

                    if ( $dados->primeiroAcesso == 0 )
                    {

                        return $this->_helper->redirector->goToRoute(array('controller' => 'auth',
                                                                           'action'     => 'senha'),
                                                                     null, true);
                    } else{
                        //Redireciona para o Controller protegido
                        return $this->_helper->redirector->goToRoute( array('controller' => 'index'), null, true);
                    }

                } else
                {
                    //Dados inválidos
                    $this->view->mensagem = "<div id='alerta'>Dados de usuário ou senha inválidos.</div>";
                }
            } else
            {
                //Formulário preenchido de forma incorreta
                $this->view->mensagem = "<div id='alerta'>Preencha o formulário corretamente.</div>";
            }
        }
    }

    /**
     * Encerra a sessão do usuario e o redireciona para a tela de login do sistema.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector('login');
    }


    /**
     * Redefine a senha do usuario que acessou o sistema pela primeira vez.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function senhaAction()
    {

        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }

        //Pega as informações do usuario logado no sistema.
        $this->usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $this->view->usuarioLogado = $this->usuarioLogado;


        if ( $this->getRequest()->isPost() )
        {
            $data = array(
                        'id'         => $this->_request->getPost('id'),
                        'senhaNova'  => $this->_request->getPost('senhaNova'),
                        'senhaNova2' => $this->_request->getPost('senhaNova2')
                    );


            if (  empty($data['senhaNova']) || empty($data['senhaNova2']) )
                $this->view->mensagem = "<div id='alerta'>Preencha os campos obrigatórios.</div>";
            elseif ( $data['senhaNova'] != $data['senhaNova2']  )
                $this->view->mensagem = "<div id='alerta'>As senhas digitadas são diferentes.</div>";
            else
            {
                //Faz o update do campo senha e redireciona para tela inicial do sistema

                $where = $this->_usuario->getAdapter()->quoteInto('idfuncionario = ?', (int) $data['id']);

                $data = array(
                    'senha' => sha1($data['senhaNova']),
                    'primeiroAcesso' => 1
                );

                $this->_usuario->update($data, $where);
                $this->_redirect('/index');
            }
        }

    }

    /**
     * Action definida apenas para referenciar o endereço 'contato' no dispatch do zend.
     *
     * @author Geovane mimoso
     * @access public
     * @return void
     *
     */
    public function contatoAction(){ }

}