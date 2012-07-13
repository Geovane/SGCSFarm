<?php

class FilialController extends Zend_Controller_Action
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

        if(!$this->adminEmpresa){
            $this->_redirect('/index/negado');
        }


        
        $this->funcionario = new Model_DbTable_Func();
        $this->filial = new Model_DbTable_Filial();
        $this->colaboradores = new Model_DbTable_Colaboradores();
        $this->projeto = new Model_DbTable_Proj();
    }

  /** 
     * Função inicial do controller tarefas.
     * envia ao seu view as informações sobre os projetos.
     * 
     * @access public 
     * @return void
     */
    
       public function indexAction()
    {
         $this->view->flag = $this->_request->getParam('flag');
         $select = $this->filial->select();
         $this->view->filial = $this->filial->fetchAll($select);
    }

    /** 
     * Função responsável pela inserção ao banco dos dados de uma nova tarefa
     * criada vinculada a um colaborador em um projeto.
     * 
     * @access public 
     * @return void
     */
    
     public function createAction()
    {

        $this->view->flag = $this->_request->getParam('flag');
        $func = $this->funcionario->select()
                ->where('empresaFilial_idempresaFilial = ?', 0);
        $this->view->funcionario = $this->funcionario->fetchAll( $func );
        //$this->view->empresa= $this->empresa->fetchAll();

        
            if ( $this->_request->isPost() )
            {

                $empresa = $this->filial->select()
                        ->where('empresa_idempresa = ?', $this->idEmpresa)
                        ->where('nome = ?', $this->_request->getPost('nome'));

                $numero = count($this->funcionario->fetchAll($empresa));

                if( $numero == 0){
                    
                    $data = array(
                        'nome'  => $this->_request->getPost('nome'),
                        'tel' => $this->_request->getPost('tel'),
                        'endereco'  => $this->_request->getPost('endereco'),
                        'responsavel'  => $this->_request->getPost('idResponsavel'),
                        'empresa_idempresa' => $this->idEmpresa,
                        'email'  => $this->_request->getPost('email'),
                        'cep' => $this->_request->getPost('cep')
                    );



                   //Insere empresa filial
                   $idInserido = $this->filial->insert($data);

                   //Atribuindo a filial recem criada ao responsavel
                   $data1 = array(
                             'empresaFilial_idempresaFilial' => $idInserido
                   );

                    $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('idResponsavel'));

                    $this->funcionario->update($data1, $where);

                    $this->_redirect('/filial/index/flag/1');

                }else{

                   $this->_redirect('/filial/create/flag/1');
                    
                }
            }

    }

    public function editAction(){


            if ( $this->_request->isPost() )
            {
                //Teste para verificação se o nome da empresa filial esta sendo repetido
                if($this->_request->getPost('nome')== $this->_request->getPost('filialnome') )
                {
                   $numero = 0;
                   
                }else{
                    $filial = $this->filial->select()
                    ->where('empresa_idempresa = ?', $this->idEmpresa)
                    ->where('nome = ?', $this->_request->getPost('nome'));

                    $numero = count($this->filial->fetchAll($filial));
                }

               if( $numero == 0){

                    if( $this->_request->getPost('idResponsavel') ==  $this->_request->getPost('idresp'))
                    {
                    
                            $data = array(
                                'nome'  => $this->_request->getPost('nome'),
                                'tel' => $this->_request->getPost('tel'),
                                'endereco'  => $this->_request->getPost('endereco'),
                                'email'  => $this->_request->getPost('email'),
                                'cep' => $this->_request->getPost('cep')
                            );

                            //print_r($data);
                    
                           //Atualiza empresa filial
                           $where = $this->filial->getAdapter()->quoteInto('idempresaFilial = ?', (int) $this->_request->getPost('idfilial'));
                           $this->filial->update($data, $where);

                           //Informa a atualização da filial
                           $this->_redirect('/filial/index/flag/2');
                 }else{

                    //pega o id do responsavel antigo
                    $id = $this->_request->getPost('idresp');

                    //Verifica se o funcionario esta inserido como colaborador em algum projeto
                    $select = $this->colaboradores->select();
                    $select->from($this->colaboradores, 'COUNT(*) AS num');
                    $select->where('funcionario_idfuncionario = ?', $id);

                    //Verifica se o funcionario é gerente de algum projeto
                    $selectP = $this->projeto->select();
                    $selectP->from($this->projeto, 'COUNT(*) AS num');
                    $selectP->where('idGerente = ?', $id);


                        if($this->colaboradores->fetchRow($select)->num == 0 && $this->projeto->fetchRow($selectP)->num == 0 )
                        {
                                $data = array(
                                    'nome'  => $this->_request->getPost('nome'),
                                    'tel' => $this->_request->getPost('tel'),
                                    'endereco'  => $this->_request->getPost('endereco'),
                                    'responsavel'  => $this->_request->getPost('idResponsavel'),
                                    'email'  => $this->_request->getPost('email'),
                                    'cep' => $this->_request->getPost('cep')
                                );

                               //Atualiza empresa filial
                               $where = $this->filial->getAdapter()->quoteInto('idempresaFilial = ?', (int) $this->_request->getPost('idfilial'));
                               $this->filial->update($data, $where);

                              //Atribuindo a filial ao novo responsavel
                               $data1 = array(
                                 'empresaFilial_idempresaFilial' => $this->_request->getPost('idfilial')
                               );

                               $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('idResponsavel'));
                               $this->funcionario->update($data1, $where);

                              //Retirando a filial do responsavel anterior
                               if($this->_request->getPost('idresp') != $this->idFunc){

                                       $data2 = array(
                                         'empresaFilial_idempresaFilial' => 0
                                       );

                                       $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $this->_request->getPost('idresp'));
                                       $this->funcionario->update($data2, $where);
                               }

                               //Informa a atualização da filial e a troca de responsavel
                               $this->_redirect('/filial/index/flag/4');
                        }else{

                             //Erro: o responsavel não pode ser trocado pois ele esta vinculado
                             $this->_redirect('/filial/edit/id/'.$this->_request->getPost('idfilial').'/flag/2');
                        }
                    }
                }else{
                   //flag de erro: mostra quando o nome editado já existe no banco
                   $this->_redirect('/filial/edit/id/'.$this->_request->getPost('idfilial').'/flag/1');

                }
            
            }else{

               $this->view->flag = $this->_request->getParam('flag');

               $filial_id = $this->_getParam('id');
               $result  = $this->filial->find($filial_id);
               $filial = $result->current();
               $this->view->filial = $filial;

               $resp = $this->funcionario->select()
                        ->where('idfuncionario = ?', $filial->responsavel);

               $func = $this->funcionario->select()
                        ->where('empresaFilial_idempresaFilial = ?', 0);

               $this->view->responsavel = $this->funcionario->fetchRow( $resp );
               $this->view->funcionario = $this->funcionario->fetchAll( $func );

            }

    }


     public function deleteAction(){

        $filial_id = $this->_getParam('id');

            //Verifica se existem funcionarios relacionados aquela filial
            $select = $this->funcionario->select();
            $select->from($this->funcionario, 'COUNT(*) AS num');
            $select->where('empresaFilial_idempresaFilial = ?', $filial_id);

            if ($this->funcionario->fetchRow($select)->num == 1 )
            {
               $filial_id = $this->_getParam('id');
               $result  = $this->filial->find($filial_id);
               $filial = $result->current();

                //Verifica se o funcionario esta inserido como colaborador em algum projeto
                $selectC = $this->colaboradores->select();
                $selectC->from($this->colaboradores, 'COUNT(*) AS num');
                $selectC->where('funcionario_idfuncionario = ?', $filial->responsavel);

                //Verifica se o funcionario é gerente de algum projeto
                $selectP = $this->projeto->select();
                $selectP->from($this->projeto, 'COUNT(*) AS num');
                $selectP->where('idGerente = ?', $filial->responsavel);


                if($this->colaboradores->fetchRow($selectC)->num == 0 && $this->projeto->fetchRow($selectP)->num == 0 ){

                      if($filial->idempresaFilial != $this->idFilial ){
                           //Retirando a filial do responsavel pela filial excluida
                           $data = array(
                             'empresaFilial_idempresaFilial' => 0
                           );

                           $where = $this->funcionario->getAdapter()->quoteInto('idfuncionario = ?', (int) $filial->responsavel);
                           $this->funcionario->update($data, $where);


                           //deleta o funcionario
                           $where = $this->filial->getAdapter()->quoteInto(' idempresaFilial = ?', $filial_id);
                           $this->filial->delete($where);

                           //Mensagem que informa a exclusão da filial
                           $this->_redirect('filial/index/flag/3');
                        }else{
                            //Filial não pode ser excluida pois é a filial matriz da empresa
                            $this->_redirect('filial/index/flag/7');
                        }
                }else{

                    //Mensagem que informa a exclusão da filial
                     $this->_redirect('filial/index/flag/5');

                }

            }else
            {
              //Mensagem que informa que a flilial não pode ser excluida pois tem,alem do responsavel,
              //outros funcinarios relacionadas a ela;
              $this->_redirect('filial/index/flag/6');
            }

    }

}





