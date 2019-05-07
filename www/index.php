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
require 'vendor/autoload.php';
// get back sessions_var
session_start();

$oSmarty = new Smarty();
$oSmarty->template_dir = 'templates/';
$oSmarty->compile_dir  = 'templates_c/';
$oSmarty->config_dir   = 'templates/languages/';


if (!file_exists('config/config.php')) {
    echo "Config not done !";
    header('Location: install/setup.php');
}

require 'config/config.php';

if ($_SESSION['bLogged'] == null) {
    header("Location: login.php");
}

$sError="";
$sFile = "index/".LANG.".txt";
$oSmarty->assign('File', $sFile);
$oSmarty->assign('Version', VERSION);
$oSmarty->assign('Error', $sError);
$oSmarty->display("index.smarty");
