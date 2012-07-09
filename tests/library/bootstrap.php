<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Luiz Daud
 */
// TODO: check include path
//ini_set('include_path', ini_get('include_path'));

// put your code here

$rootPath = realpath(dirname(__DIR__));
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH',
        $rootPath . '/application');
}
if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', 'testing');
    }
set_include_path(implode(PATH_SEPARATOR, array(
    '.',
$rootPath . '/library',
    get_include_path(),
    )));
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('Custom_');
?>
