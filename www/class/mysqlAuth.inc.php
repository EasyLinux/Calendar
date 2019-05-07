<?php

/**
 * This is the plugin backend for mysql/mariadb authentication
 *
 * @package		Edicod
 * @subpackage          Framework
 */
class authClass
{
    
    // User data
    private $uid ;
    private $UserName ;
    private $GivenName ;
    private $Login ;
    private $Password;
    private $Email ;
    private $Valid ;

    // Base
    private $Db;

    // Profile data
    private $pid;
    private $ProfileName;
    private $Rights;

    // Status
    private $Loggued ;
    private $ErrorString ;
    private $ErrorId ;

    // User's group
    private $Groups=array();
    private $Guids;

    public function setConfig($Config)
    {
        $return = true;
        $this->Db = $Config['database'];

        $this->Db['Mysqli'] = new mysqli(
            $this->Db['host'],
            $this->Db['user'],
            $this->Db['passwd'],
            $this->Db['base']
        );

        if ($this->Db['Mysqli']->connect_errno != 0) {
            $return = "ERROR(".$this->Db['Mysqli']->connect_errno
                                . ") ". $this->Db['Mysqli']->connect_error;
        }
        return $return;
    }
        
    public function setUser($User)
    {
        $this->Login = $User;
    }

    public function getUser()
    {
        return $this->Login;
    }

    /**
     *
     *
     * @param	  string	$Password    User password
   * @return	bool		True if Login/Password crendentials are good
   */
    public function isValid($Password)
    {
        $return = true;
        $MD5Pwd = MD5($Password);
        $Sql = "SELECT login, name, given_name, email, valid, uid, pid "
               . "FROM user "
             . "WHERE login = ? AND MD5Pass = ? AND valid=1 ";
        $stmt = $this->Db['Mysqli']->prepare($Sql);
        $stmt->bind_param("ss", $this->Login, $MD5Pwd);
        $stmt->execute();


        $stmt->bind_result(
          $this->Login,
          $this->UserName,
          $this->GivenName,
          $this->Email,
          $this->Valid,
          $this->uid,
          $this->pid
      );
        $stmt->store_result();
        if ($stmt->num_rows != 1) {
            return false;
        } else {
            $stmt->fetch();
            $this->Loggued = true ;
        }
        return true;
    }

    public function isLogged($MD5Pwd)
    {
        return $this->Loggued ;
    }

    /**
     * Récupère les informations de l'utilisateur
     * @package		Edicod
     *
     */
    public function loadProfileData()
    {  // charge les donnees du profil utilisateur
        $tGuids=array();
        if ($this->Loggued === false) {
            return false ;
        }
        
        $Sql  = "SELECT description, rights "
            . "FROM profiles "
            . "WHERE pid= ?";
        $stmt = $this->Db['Mysqli']->prepare($Sql);
        $stmt->bind_param("i", $this->pid);
        $stmt->execute();
        $stmt->bind_result($this->ProfileName, $this->Rights);
        $stmt->store_result();
    
        $Sql = "SELECT gid FROM g_grp "
           . "WHERE uid=?;";
    
        $stmt = $this->Db['Mysqli']->prepare($Sql);
        $stmt->bind_param("i", $this->uid);
        $stmt->execute();
        $stmt->bind_result($gid);
        // Créer variable Guids
        $tGuids[] = "U". $this->uid;
        while ($stmt->fetch()) {
            $tGuids[] = "G". $gid;
            $this->Groups[] = $gid;
        }
        $this->Guids = json_encode($tGuids);
    }

    /**
     * Récupère les groupes de l'utilisateur
     * @package		Edicod
     * @param		objet		Pointe sur l'objet de bdd
     */
    public function ListUserGroups($Db)
    {  // charge les donnees du profil utilisateur
        $requete = "SELECT * FROM groups,g_grp WHERE g_grp.gid=groups.gid AND uid=".$this->uid.";";
        $Db->Query($requete) ;
        return($Db->loadObjectList());
    }

    /**
     * Récupère la liste des utilisateurs
     *
     * @package		Edicod
     * @return		tableau		Liste des utilisateurs
     */
    public function GetUserList()
    {
        $Sql = "SELECT uid, name, given_name FROM user ORDER BY name, given_name";
        $Res = mysql_query($Sql, $this->Db);
        $array = array();
        while ($row = mysql_fetch_array($Res, MYSQL_ASSOC)) {
            $array[] = $row;
        }
        mysql_free_result($Res);  // Libère la mémoire
        return $array;
    }

    /**
     * Ecrit les données utilisateurs dans la variable de session
     *
     * @package		Edicod
     *
     * @return		void
     */
    public function writeSession()
    {
        $_SESSION['IsLoggued']           = $this->Loggued;
        $_SESSION['User']['Login']       = $this->Login ;
        $_SESSION['User']['Passwd']      = $this->Password;
        $_SESSION['User']['UserName']    = $this->UserName ;
        $_SESSION['User']['GivenName']   = $this->GivenName ;
        $_SESSION['User']['Email']       = $this->Email ;
        $_SESSION['User']['uid']         = $this->uid ;
        $_SESSION['User']['ProfileName'] = $this->ProfileName ;
        $_SESSION['User']['Rights']      = $this->Rights ;
        $_SESSION['User']['IsLoggued']   = $this->Loggued;
        $_SESSION['User']['Guids']       = $this->Guids;
        $_SESSION['User']['Groups']      = $this->Groups;
    }


    public function getError()
    {
        return $this->ErrorId ;
    }
 
    /**
     *  Retrouve la liste des groupes
     *
     * @todo a modifier avec prefix pour utiliser des groupes windows ou ...
     * @todo a modifier avec plug-in
     */
    public function getGroupList()
    {
        $Sql = "SELECT * FROM groups;";
        $this->Db->Query($Sql);
        return($this->Db->LoadObjectList());
    }

    /**
     *  Genere une liste de guid et sélectionne le guid passé en paramètre
     * @todo a modifier avec prefix
     * @todo a modifier avec plug-in
     */
    public function getGuidOption($Indent, $Guid)
    {
        $Html="";
        $Chk = "";
        $Sql = "SELECT * FROM groups ORDER BY name;";
        $this->Db->Query($Sql);
        $Groups = $this->Db->loadObjectList();
        foreach ($Groups as $Group) {
            $sGuid = "G". $Group->gid;
            $sName = "G: ". $Group->name;
            if (($sGuid) == $Guid) {
                $Chk = "selected='selected'";
            } else {
                $Chk="";
            }
            $Html .= $Indent . "<option value='$sGuid' $Chk>$sName</option>\n";
        }
        $Sql = "SELECT * FROM user ORDER BY name+given_name;";
        $this->Db->Query($Sql);
        $Users = $this->Db->loadObjectList();
        foreach ($Users as $User) {
            $sGuid = "U". $User->uid;
            $sName = "U: ". $User->name . " ". $User->given_name;
            if (($sGuid) == $Guid) {
                $Chk = "selected='selected'";
            } else {
                $Chk="";
            }
            $Html .= $Indent . "<option value='$sGuid' $Chk>$sName</option>\n";
        }
        return($Html);
    }
    
    /**
     * Retourne les caractéristiques de l'utilisateur dont l'ui est passé en paramètre
     *
     * @param	int	uid  uid de l'utilisateur
     * @return    objet   objet utilisateur
     *
    public function GetUser($uid)
      {
      $Sql = "SELECT * FROM user WHERE uid=$uid;";
      $this->Db->Query($Sql);
      return $this->Db->loadObject();
      }
      */
}
