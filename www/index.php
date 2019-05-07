<?php
/**
 * Point de départ du programme
 * php version 7.2
 * 
 * @category Webapp
 * @package  Project
 * @author   Serge NOEL <serge.noel@easylinux.fr>
 * @license  GNU GPL
 * @link     http://www.easylinux.fr/calendar
 */
require __DIR__ . '/vendor/autoload.php';

$smarty = new Smarty();
$smarty->template_dir = 'templates/';
$smarty->compile_dir  = 'templates_c/';
$smarty->config_dir   = 'templates/languages/';


if (!file_exists('config/config.php')) {
    echo "Config not done !";
    header('Location: install/setup.php');
}
echo file_get_contents("templates/index.smarty");
//header("Location: calendarserver.php/");
//echo getcwd() . " " . $_SERVER['DOCUMENT_ROOT'];