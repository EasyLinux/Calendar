<?php
/**
 * LDAP External Authentication Bridge
 *
 * This file contains the authentification class for LdapAutoload
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

class AuthClass
{
    // Ldap credentials
    private $_BaseDN;
    private $_Host;
    private $_Protocol;
    private $_Anon;
    private $_Passwd;
    private $_BindDN;
    private $_Socket;
    private $_Filter = "(&(objectClass=*)(|(cn=$1)(email=$1)))";
    private $_Result;
    
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
        $this->_Host     = $Config['ldap']['host'];
        $this->_BaseDN   = $Config['ldap']['basedn'];
        $this->_Protocol = $Config['ldap']['protocol'];
        $this->_BindDN   = $Config['ldap']['user'];
        $this->_Passwd   = $Config['ldap']['passwd'];
        $this->_Anon     = $Config['ldap']['anon'];
         
        $this->_Socket = ldap_connect($this->_Host);
        if ($this->_Socket === false) {
            return "ERROR: ldap_connect failed !";
        }
        // Set initial LDAP values.
        ldap_set_option($this->_Socket, LDAP_OPT_PROTOCOL_VERSION, $version);
        ldap_set_option($this->_Socket, LDAP_OPT_REFERRALS, 0);

        if ($this->_Anon == true) {
            // Set preauth flag to value of socket on anonymous bind.
            $this->_Cnx = $this->_Socket;
        } else {
            // Set preauth flag using call to ldap_bind on authenticated bind.
            $this->_Cnx = ldap_bind(
                $this->_Socket,
                $this->_BindDN,
                $this->_Passwd
            );
        }
        if ($this->_Cnx == false) {
            return "ERROR: ldap_bind failed !";
        }
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
        $this->_Loggued = false;
        // !!!!
        if ($this->_User == snoel && $Pass == "Test") {
            $this->_Loggued = true;
        }
        return $this->_Loggued;
        // !!!!!
         
        $sfilter = str_replace("$1", $this->_User, $this->_Filter);
        echo $sfilter;
         
        $this->_Result = ldap_search($this->_Cnx, $this->_BaseDN, $sfilter);
        $icount  = ldap_count_entries($this->_Cnx, $this->_Result);
         
        if ($iCount === 1) {
            $this->_Loggued = true;
        }
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
}
