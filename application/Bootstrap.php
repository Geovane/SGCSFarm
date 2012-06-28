<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initialize autoloader
     *
     * Use the setFallbackAutoloader() method to have the autoloader act
     * as a catch-all
     *
     * @return void
     */
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
                        'basePath' => APPLICATION_PATH,
                        'namespace' => ''
        ));
        return $autoloader;
    }

    /*protected function _initDoctype()
    {

        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }*/
    
    protected function _initView()
    {
        $view = new Zend_View ();
        $view->addHelperPath ( 'ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper' );
        //ZendX_JQuery::enableView($view);
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer ();
        $viewRenderer->setView ( $view );
        Zend_Controller_Action_HelperBroker::addHelper ( $viewRenderer );
        $this->bootstrap ( 'layout' );
        $layout = $this->getResource ( 'layout' );
        $view = $layout->getView();
        
        $view->doctype('XHTML1_STRICT');
        
    }
    /*protected function _initViewHelpers() {
        $view = new Zend_View ();
        $this->bootstrap ( 'layout' );
        $layout = $this->getResource ( 'layout' );
        $view = $layout->getView ();
        $view->addHelperPath ( 'ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper' );
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer ();
        $viewRenderer->setView ( $view );
        Zend_Controller_Action_HelperBroker::addHelper ( $viewRenderer );
    }*/

}

