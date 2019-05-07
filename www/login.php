<?php
/**
 * Affiche un formulaire de login
 *   Ce fichier est appelé avant toute utilisation du programme
 *   il présente l'écran de connexion, valide l'identifiant et renvoi sur index.php en
 *   cas de succès
 * 
 *   Les donnés sont sécurisées par cryptage MD5 lors de l'envoi. <br/>
 *   <br/>
 *   Le fichier /etc/Edicod/<host>.conf est cherché et s'il est trouvé, 
 *   sert à lire la configuration du serveur
 *   sinon, /etc/Edicod/config.conf est utilisé
 * 
 *
 * @author              Serge NOEL <serge.noel@net6a.com>
 * @version             1.2
 * @package 						Edicod
 * @subpackage          Framework
 * @todo                Changer la gestion des profils par l'utilisation d'un groupe système
 * @todo                Changer la gestion valid (utilisateur) par l'utilisation d'un groupe système
 */
require('config/config.php');
require('vendor/autoload.php');
require('class/autoload.php');

$oSmarty = new Smarty();
$oSmarty->config_dir = './templates/languages/';

$File = "login/".LANG.".txt";
$Debug='';
$Error="";
// Fichiers à inclure
$Name = explode(".",$_SERVER["SERVER_NAME"]);
if( file_exists("/etc/Edicod/". $Name[0].".php") )
  {
  $Debug = "ATTENTION: utilise le fichier: /etc/Edicod/" . $Name[0] . ".php ";
  require("/etc/Edicod/". $Name[0].".php");
  }
else
  {
  $Debug = "ATTENTION: le fichier: /etc/Edicod/" . $Name[0] . 
           ".php est introuvable, utilise /etc/Edicod/config.php";
  if( file_exists("/etc/Edicod/config.php") )           
  	require('/etc/Edicod/config.php');	// Inclure uniquement ici
  else
    $Error = "Pas de fichier de configuration /etc/Edicod/config.php";
  }

// Permier appel -> affiche le formulaire
if( empty($_POST["Action"]) )
  {
  $oSmarty->assign('File',$File);
  $oSmarty->assign('Version',VERSION);
  $oSmarty->assign('Error',$Error);
  if( DEBUG )
  	$oSmarty->assign('Debug',$Debug);
  else
  	$oSmarty->assign('Debug','');
  $oSmarty->display('login.smarty');
  die();
  }

session_start();

$oUser = new User($Cfg);
$sInit = $oUser->setConfig($Cfg);

if( $sInit !== true )
  {
  $Error = $sInit;
  $oSmarty->assign('File',$File);
  $oSmarty->assign('Version',VERSION);
  $oSmarty->assign('Error',$sInit);
	$oSmarty->assign('Debug','');
  $oSmarty->display('login.smarty');
  die();
  }
// Valider compte/mot de passe
$oUser->setUser(filter_input(INPUT_POST,'Login',FILTER_SANITIZE_STRING));

$Valid = $oUser->isValid(
	           filter_input(INPUT_POST,'Password',FILTER_SANITIZE_STRING));

$Debug = "User: ". nl2br(print_r($_POST,true));

if ( $Valid == false ) 
  {
  $oSmarty->assign('File',$File);
  $oSmarty->assign('Version',VERSION);
  $oSmarty->assign('Error',"<b>ERREUR:</b> Identifiant / mot de passe incorrect");
  if( DEBUG )
  	$oSmarty->assign('Debug',$Debug);
  else
  	$oSmarty->assign('Debug','');
  $oSmarty->display('login.smarty');
  die();
  }

// Arrivé ici, l'utilisateur est connecté 
$oUser->loadProfileData() ;
$oUser->writeSession() ;

$_SESSION['Cfg'] = $Cfg;
$oDb = new db($Cfg['database']);
$oDb->getParameters();
// Retour à la page d'accueil
echo nl2br(print_r($_SESSION,true));
header("Location: index.php" ); 

?>
