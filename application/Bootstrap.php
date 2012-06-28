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

    protected function _initDoctype()
    {

        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

}

