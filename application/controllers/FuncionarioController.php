<?php

class FuncionarioController extends Zend_Controller_Action
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

        /* Initialize action controller here */
        $this->funcionario = new Model_DbTable_Func();
        $this->filial = new Model_DbTable_Filial();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->projeto = new Model_DbTable_Proj();
        $this->userGit = new Model_DbTable_UserGit();
        $this->userBug = new Model_DbTable_UserBugzilla();
        $this->redefinir = new Model_DbTable_RedefinirSenha();

    }

    public function indexAction()
    {
         $this->view->flag = $this->_request->getParam('flag');
         $select = $this->funcionario->select()->order('nome');

         $rows = $this->funcionario->fetchAll($select);

         //Cria a paginação relativa a exibição dos funcionarios

         $paginator = Zend_Paginator::factory($rows);
         //Passa o numero de registros por pagina
         $paginator->setItemCountPerPage(5);
      
         $this->view->paginator = $paginator;
         $paginator->setCurrentPageNumber($this->_getParam('page'));

    }


    public function createAction()
    {

        $this->view->filial = $this->filial->fetchAll();

            if ( $this->_request->isPost() )
            {
                $data = array(
                    'nome'  => $this->_request->getPost('nome'),
                    'documentoIdentificacao'  => $this->_request->getPost('doc'),
                    'login' => $this->_request->getPost('login'),
                    'senha'  => sha1($this->_request->getPost('doc')),
                    'email'  => $this->_request->getPost('email'),
                    'empresaFilial_idempresaFilial' => $this->_request->getPost('idFilial'),
                    'foto' => '/images/fotosFunc/usuarioPadrao.jpg'
                );

                //Insere funcionario e guardo o id dele na variavel $idInserido
                $idInserido = $this->funcionario->insert($data);

                //Cria usuario Git e bugZilla
                $data1 = array(
                    'funcionario_idfuncionario'  => $idInserido,
                    'usuario'  => $this->_request->getPost('login'),
                    'senha'  => sha1($this->_request->getPost('doc'))
                );

               //Insere usuario Git e bugZilla
               //print_r($data1);

               $this->userBug->insert($data1);
               $this->userGit->insert($data1);

               $this->_redirect('funcionario/index/flag/1');
            }

    }

    public function editAction(){

       $func_id = $this->_getParam('id');



       $result  = $this->funcionario->find($func_id);
       $this->view->funcionario = $result->current();
       $this->view->filial = $this->filial->fetchAll();

            if ( $this->_request->isPost() )
            {

              if ( !empty ($file) ){

                $data = array(
                    'nome'  => $this->_request->getPost('nome'),
                    'documentoIdentificacao'  => $this->_request->getPost('doc'),
                    'login' => $this->_request->getPost('login'),
                    'email'  => $this->_request->getPost('email'),
                    'empresaFilial_idempresaFilial' => $this->_request->getPost('idFilial')
                );


                $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('id'));

                $this->funcionario->update($data, $where);

                $this->_redirect('funcionario/index/flag/2');

              }
            }

    }


     public function imagemAction(){



            if ( $this->_request->isPost() )
            {

              $idFunc = $this->funcLogado->idfuncionario;
              $permitido = array('jpg', 'jpeg', 'png', 'gif');
              $file = $this->uploadFiles('images/fotosFunc/', true , $permitido, $idFunc );

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

                $this->_redirect('funcionario/index/flag/6');

              }else{

                 $this->_redirect('funcionario/index/flag/5');

              }
               
                
            }

    }

    public function deleteAction(){

        $func_id = $this->_getParam('id');

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
                
                $this->_redirect('funcionario/index/flag/3');

            } else
            {
              $this->_redirect('funcionario/index/flag/4');
            }

    }

     /*
     * Método que deleta do banco todos os registros que envolvem o usuário
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

     private function uploadFiles($uploadDir = null, $renameFile = true , $allowedExtensions = array(), $id = "x")
            {
            //Caso o diretorio não exista , cria o diretorio para o destino
            if(!file_exists($uploadDir))
                    mkdir($uploadDir, 0777, true);

            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->setDestination($uploadDir);

            $files = $adapter->getFileInfo();

            //Pega o nome dos campos
            $fields = array_keys($files);

            //Variavel contendo tipos
            $i= 0;

            foreach($files as $info)
            {
                $filename = strtolower($info['name']) ;
                $exts = split("[/\\.]", $filename) ;
                $n = count($exts)-1;
                $exts = $exts[$n];



                    $ext = $exts;
                    if($info['name'] != '')
                    {
                            //verifica se foi passado os tipos de arquivos permitidos no parametro
                            if(count($allowedExtensions) > 0)
                            {
                                    //verifica se a extenção enviada é uma extenção valida
                                        if(!array_keys($allowedExtensions, $ext))
                                        {
                                        return null;
                                        }
                            }
                    //Renomeia o Arquivo
                    //Caso o arquivo tenha sido carregado, insere o nome do arquivo ao array
                    //Ele tendo sido sobescrito ou não

                    if($renameFile)
                    {
                            $fileName = $id."_".time().".".$ext;
                            $adapter->addFilter('Rename', array('target' => $uploadDir.DIRECTORY_SEPARATOR.$fileName,'overwrite' => true));
                            $filesUploaded[] = $fileName;
                    }
                    else
                        $filesUploaed[] = $info['name'];

                        $adapter->receive($info['name']);
            }
        $i++;
       }

       return $filesUploaded;
     }

}