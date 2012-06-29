<?php

class AuthController extends Zend_Controller_Action
{
    private $_usuario;

    public function init()
    {
        $this->_usuario = new Model_DbTable_Func();

        // Seta layout de Login
        $this->_helper->layout->setLayout('login');

    }

    public function indexAction()
    {
        return $this->_helper->redirector('login');
    }

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
                                                                'documentoIdentificacao',
                                                                'email',
                                                                'empresaFilial_idempresaFilial'
                                                             )
                            );

                    $storage = $auth->getStorage();
                    $storage->write($info);

                    /*Verifica se é o primeiro login.
                     * Se for, redireciona para o usuário fazer a alteração da senha
                     * Senão, registra a data e hora no campo ultimo_login

                    $usuario = Zend_Auth::getInstance()->getIdentity();

                    $result  = $this->_usuario->find($usuario->id);
                    $dados = $result->current();

                    if ( $dados->ultimo_login == NULL )
                    {
                        $where = $this->_usuario->getAdapter()->quoteInto('id = ?', (int) $dados->id);

                        $data = array( 'ultimo_login' => date('y-m-d h:i:s'));
                        $this->_usuario->update($data, $where);

                        return $this->_helper->redirector->goToRoute(array('controller' => 'meu-cadastro',
                                                                           'action'     => 'senha'),
                                                                     null, true);
                    } else
                     */


                    //{
                        //Redireciona para o Controller protegido
                        return $this->_helper->redirector->goToRoute( array('controller' => 'index'), null, true);
                    //}

                } else
                {
                    //Dados inválidos
                    $this->view->mensagem = 'Dados de usuário ou senha inválidos.';
                }
            } else
            {
                //Formulário preenchido de forma incorreta
                $this->view->mensagem = 'Preencha o formulário corretamente.';
            }
        }
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector('login');
    }

}