<?php
/**
 * Autoload
 *   Ce fichier charge les classes du répertoire courant
 *   php version 7.2
 * 
 * @category Project
 * @package  Calendar
 * @author   Serge NOEL <serge.noel@net6a.com>
 * @license  GNU GPL
 * @version  GIT:1.0.0
 * @link     http://www.easylinux.fr/Calendar
 * @todo     Changer la gestion des profils par l'utilisation d'un groupe système
 * @todo     Changer la gestion valid (utilisateur) par l'utilisation d'un 
 *           groupe système
 */
$aFiles = scandir('class/');
foreach ( $aFiles as $sFile) {
    if (strpos($sFile, ".class.php") !== false) {
        include_once $sFile ;
    }
}
