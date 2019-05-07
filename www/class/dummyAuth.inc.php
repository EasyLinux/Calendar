<?php
/**
 * Dummy External Authentication
 *
 * This file contains the authentification class for DummyAutoload
 * php version 7.2
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

 /**
  * Dummy class provide a skeleton 
  *
  * @category Project
  * @package  Calendar
  * @author   Serge NOEL <serge.noel@easylinux.fr>
  * @license  GNU GPL
  * @link     http://www.easylinux.fr/Calendar
  */
class AuthClass
{
    
    // User datas
    private $_User;
    private $_uid ;
    private $_UserName ;
    private $_GivenName ;
    private $_Login ;
    private $_Password;
    private $_Email ;
    private $_Valid ;

    // Profile data
    private $_pid;
    private $_ProfileName;
    private $_Rights;

    // Status
    private $_Loggued ;
    private $_ErrorString ;
    private $_ErrorId ;

    // User's groups
    private $_Groups=array();
    private $_Guids="";
     
    /**
     * Initialize auth plugin
     *
     * @param array $aConfig config datas
     *
     * @return bool/string      true if result OK
     *                                Error message if failed
     */
    public function setConfig($aConfig)
    {
        return(true);
    }
     
    /**
     * Set user
     *
     * @param string $sUser Username
     *
     * @return int
     */
    public function setUser($sUser)
    {
        $this->_User = $sUser;
    }

    /**
     * Function getUser retreive current user
     *
     * @return void
     */
    public function getUser()
    {
        return $this->_User;
    }
     
    /**
     * Try to authenticate with credentials
     *   username is filled by setUser method
     *
     * @param string $Pass Password of User
     *
     * @return bool   true if User/Password is valid
     */
    public function isValid($Pass)
    {
        $this->_Loggued = true;
        $this->_User = "snoel";
        return $this->_Loggued;
    }

    /**
     * Get back User datas from backend
     *
     * @return void
     */
    public function loadProfileData()
    {
        // charge les donnees du profil utilisateur
        if ($this->_Loggued === false) {
            return false;
        }
        
        $this->_ProfileName = 'Admin';
        $this->_Rights      = 'all';
    }

    /**
     * Save User datas in php Session
     *
     * @return void
     */
    public function writeSession()
    {
        // charge les donnees du profil utilisateur
        if ($this->_Loggued === false) {
            return false;
        }
        
        $_SESSION['bLogged'] = true;
        $_SESSION["User"] = [
            "name" => "NOEL",
            "given" => "Serge",
            "login" => "snoel",
            "cal"   => "Pro"
        ];
    }
}
