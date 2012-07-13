<?php

class PessoalController extends Zend_Controller_Action
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
    }

    public function editAction(){

       $result  = $this->funcionario->find($this->idFunc);
       $this->view->funcionario = $result->current();

            if ( $this->_request->isPost() )
            {

                $data = array(
                    'nome'  => $this->_request->getPost('nome'),
                    'email'  => $this->_request->getPost('email'),
                );

                $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->idFunc);

                $this->funcionario->update($data, $where);

                $this->_redirect('/pessoal/meusdados/flag/1');
            }

    }

     public function imagemAction(){

        $this->view->flag = $this->_request->getParam('flag');

        if ( $this->_request->isPost() )
        {

          $idFunc = $this->funcLogado->idfuncionario;
          $permitido = array('jpg', 'jpeg', 'png', 'gif');
          $file = Model_Upload::uploadFiles('images/fotosFunc/', true , $permitido, $idFunc );

          if ( !empty ($file) ){

            //Redimensionando imagem

             $data1 = array(
                    'valor' => '/images/fotosFunc/' . $file[0]
             );

            require('../public/scripts/wideimage-11.02.19-full/lib/WideImage.php');
            $image = wideImage::load('../public' . $data1['valor']);
            //tamanho da imagem
            $image = $image->resize(102, 127);
            $image->saveToFile('../public' . $data1['valor']);

            $data = array(
                'foto' => '/images/fotosFunc/'.$file[0]
            );

            $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $idFunc);
            $this->funcionario->update($data, $where);

            $this->_redirect('/pessoal/meusdados/flag/2');

          }else{

             $this->_redirect('/pessoal/imagem/flag/1');

          }
        }

    }

     public function meusdadosAction(){

        $this->view->flag = $this->_request->getParam('flag');
        $dados = $this->funcionario->find($this->idFunc);
        $this->view->dados = $dados[0];
       
     }

    public function senhaAction()
    {

        if ( $this->getRequest()->isPost() )
        {
            $data = array(
                        'id'         =>$this->idFunc,
                        'senhaAtual' => $this->_request->getPost('senhaAtual'),
                        'senhaNova'  => $this->_request->getPost('senhaNova'),
                        'senhaNova2' => $this->_request->getPost('senhaNova2')
                    );

            $select = $this->funcionario->select();
            $select->from($this->funcionario, 'senha')
                   ->where('idfuncionario = ?', $data['id']);
            $result = $this->funcionario->fetchRow($select);

            if ( empty($data['senhaAtual']) || empty($data['senhaNova'])
                 || empty($data['senhaNova2']) )
                $this->view->mensagem = "<div id='alerta'>Preencha os campos obrigatórios.</div>";
            elseif ( sha1($data['senhaAtual']) != $result['senha'] )
                $this->view->mensagem = "<div id='alerta'>Senha atual errada.</div>";
            elseif ( $data['senhaNova'] != $data['senhaNova2']  )
                $this->view->mensagem = "<div id='alerta'>As senhas digitadas são diferentes.</div>";
            else
            {
                $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $data['id']);

                $data = array(
                    'senha' => sha1($data['senhaNova']),
                );

                $this->funcionario->update($data, $where);
                $this->_redirect('/pessoal/meusdados/flag/3');
            }
        }

    }
    
}