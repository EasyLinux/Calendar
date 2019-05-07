<?php
/**
 * Gestion du menu
 * php version 7.2
 * 
 * @category Webapp
 * @package  Project
 * @author   Serge NOEL <serge.noel@easylinux.fr>
 * @license  GNU GPL
 * @link     http://www.easylinux.fr/calendar
 */
require '../vendor/autoload.php';
// get back sessions_var
session_start();

switch( $_POST['Action'])
{
case 'Logout':
    session_destroy();
    echo "OUT";
    break;

default:
    var_dump($_POST);
    echo "Rien compris";

}