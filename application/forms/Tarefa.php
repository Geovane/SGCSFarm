<?php

class Application_Form_Tarefa extends Zend_Form
{

    public function init()
    {
        /**
         * Definições para o FORM
         */
        $this->setName( 'tarefa' );
        $this->setAction( '/tarefa/create' );
        $this->setMethod( 'post' );
        $this->setAttrib( 'enctype', 'multipart/form-data' );
        $this->setAttrib( 'id', 'form-tarefa' );
        
        /**
         * Elementos do formulário
         */
        $descricao = new Zend_Form_Element_Textarea( 'descricao' );
        $descricao->setLabel( 'Descrição: ' )
                ->setRequired(true)
                ->addValidator( 'NotEmpty' )
                ->addFilter( 'StripTags' )
                ->addFilter( 'StringTrim' )
                ->addErrorMessage('Descrição obrigatória');
        
        $dataInc = new ZendX_JQuery_Form_Element_DatePicker('dataInc');
        $dataInc->setLabel( 'Data de Início: ' )
                ->addValidator( new Zend_Validate_Date ());
        
        $dataFim = new ZendX_JQuery_Form_Element_DatePicker('dataFim');
        $dataFim->setLabel( 'Data de Fim: ' )
                ->addValidator( new Zend_Validate_Date ());
        
        $dataEntrega = new ZendX_JQuery_Form_Element_DatePicker('dataEntrega');
        $dataEntrega->setLabel( 'Data de Entrega: ' )
                ->addValidator( new Zend_Validate_Date ());
        
        $submit = new Zend_Form_Element_Submit( 'submit' );
        $submit->setLabel( 'Enviar' );
        $this->addElements(
            array(
                $descricao,
                $dataInc,
                $dataFim,
                $dataEntrega,
                $submit
            )
        );
    }


}

