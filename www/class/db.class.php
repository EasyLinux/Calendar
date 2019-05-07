<?php
/** 
 * Gestion de base de données 
 *
 * @version		1.2
 * @package		Edicod
 * @subpackage          Framework
 * @copyright		Copyright (C) 2005 - 2009 Serge NOEL. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * @tutorial		Db.pkg
 *
 * @todo                Ajout d'autres type de connexion via plug-in
 * @todo                Nettoyer le fichier
 */

/** 
 * Classe d'accès aux données
 *  	<b>Attention</b> Beaucoup de méthode sont obsolètes et normalement inutilisées
 *      
 *
 * @package		Edicod
 * @subpackage		Framework
 */
class db {

  // infomations de connexion a la base
  private $host ;
  private $base ;
  private $user ;
  private $pass ;
  private $port ;

  //parametres de transition de la base.
  private $Mysqli ;
  private $Query ;
  public  $Error ;
	
	
  function __construct ($Cfg) 
  {
	  $this->Error = false;
    $this->port = '3306';
    $this->host = $Cfg['host'];
   	$this->user = $Cfg['user'];
   	$this->pass = $Cfg['passwd'];
   	$this->base = $Cfg['base'];

    	$this->Mysqli = new mysqli($this->host, $this->user,
    							$this->pass, $this->base);

    	if( $this->Mysqli->connect_errno != 0 )
    	{
    		$this->Error = "ERROR(".$this->Mysqli->connect_errno
    							. ") ". $this->Mysqli->connect_error;
    	}
    }

  /**
   * Exécuter une requête
   *
   * @param	string		Requete SQL
   * @return	void		Rien
   */
   public function Query($Sql)
     {
     switch($this->DBENGINE)
       {
       case "MYSQL":
         $this->QUERY = $Sql; // for debug
         $this->DBQUERY = mysql_query($Sql,$this->DBCONNEXION);
         if( $this->DEBUG && $this->DBQUERY == NULL )
           {
           print "Sql : " . $this->QUERY . "<br />\n";
           $Traces = debug_backtrace();
           foreach( $Traces as $Trace )
             print $Trace["file"] . " : " . $Trace["function"] . "(" . $Trace["line"] . ")<br />";

           print "ERREUR: ". mysql_errno($this->DBCONNEXION) . " " .mysql_error($this->DBCONNEXION) . "<br />$Sql";
           }
         break;

       case "POSTGRESQL":
         $this->DBQUERY = @pg_query($Sql,$this->DBCONNEXION);
         break;
       
       default:
         break;
       }
     }	

  /**
   * Obsolète Exécuter une requête (<b>utiliser Query à la place</b>)
   *
   * @param	string		Requete SQL
   * @return	void		Rien
   */
  public function db_query ($query) 
  {
  if($this->DBENGINE == "MYSQL") 
    {
    print "<h2>Appel de db_query</h2>";
    $Traces = debug_backtrace();
    foreach( $Traces as $Trace )
      print $Trace["file"] . " : " . $Trace["function"] . "(" . $Trace["line"] . ")<br />";
    $this->DBQUERY = mysql_query($query,$this->DBCONNEXION) ;
		
		return $this->DBQUERY ;
		}elseif($this->DBENGINE == "SQLSERVER"){
			
			 $this->DBQUERY = mssql_query($query,$this->DBCONNEXION);
			return mysql_error($this->DBCONNEXION) ;
			
		}elseif ($this->DBENGINE == "POSTGRESQL") {
			
			$this->DBQUERY = @pg_query($query,$this->DBCONNEXION);
			
		}elseif ($this->DBENGINE == "ORACLE") {
			
			// pas encore de code
		}
		
	}

  /**
   *  Retourne le dernier ID attribué
   *
   *  @return	integer		Numéro de l'ID
   */
  function GetLastId()
    {
    return(mysql_insert_id());
    }

  /**
   *  Retourne le nombre de ligne trouvées
   *
   * @return	integer		Nombre de lignes
   */
  function NumRows()
    {
    return(mysql_num_rows($this->DBQUERY));
    }

  /** 
   *  Lire les paramètres 
   *    Cette méthode lit les paramètres contenus dans la table parameters
   *    et défini la variable de session parameters. 
   *    Cette méthode est appelée à l'initialisation de Edicod
   *
   * @param	void	aucun
   * @return	void	aucun
   */
  function getParameters()
    {
    $Sql = "SELECT name, value FROM parameters;";
    $stmt = $this->Mysqli->prepare($Sql);
    $stmt->execute();
    $stmt->bind_result($name,$value);
    $_SESSION['Parameters'] = array();
    while ($stmt->fetch() ) 
      $_SESSION['Parameters'][$name] = $value;
    }
   
  /**
   * Retourne un objet associatif liè à la dernière requète SQl
   *
   * @param	void	aucun
   * @return	object	objet contenant le résultat
   */	
  function loadObjectList( )
    {
    $array = array();
    while ($row = mysql_fetch_object( $this->DBQUERY )) 
      $array[] = $row;
    mysql_free_result( $this->DBQUERY );  // Libère la mémoire
    return $array;
    }

  /**
   * Retourne un tableau associatif liè à la dernière requète SQl
   * 
   * @param	void	aucun
   * @return	array	tableau contenant le résultat
   */	
  function loadArrayList( )
    {
    $array = array();
    while ($row = mysql_fetch_array( $this->DBQUERY, MYSQL_BOTH )) 
      $array[] = $row;
    mysql_free_result( $this->DBQUERY );  // Libère la mémoire
    return $array;
    }

  /** 
   * Retourne l'enregistrement courant sous forme d'un objet
   *
   * @param	void	aucun
   * @return	objet	Enregistrement trouvé
   */
  function loadObject( )
    {
    $array = mysql_fetch_object( $this->DBQUERY );
    return $array;
    }

  /** 
   * Retourne l'enregistrement courant sous forme d'un tableau associatif
   *
   * @param	void	aucun
   * @return	array	Enregistrement trouvé
   */
  function loadArray( )
    {
    $array = mysql_fetch_array( $this->DBQUERY , MYSQL_BOTH);
    return $array;
    }
	
	/**
	 * fonction donnant le nombre de ligne de résultat de la requête
	 * obsolète
	 **/
	
	public function nb_result()
	{
	
		if($this->DBENGINE == "MYSQL")
		{
           return mysql_num_rows($this->DBQUERY);
		 }
		elseif($this->DBENGINE == "SQLSERVER")
		{
			return @mssql_num_rows($this->DBQUERY);
		}
		elseif($this->DBENGINE == "POSTGRESQL")
		{
			return @pg_num_rows($this->DBQUERY);
		}
		elseif($this->DBENGINE == "ORACLE" ) {
		
			//pas encore de code
			
			
		}
	}
	

   /**
    * Lit la valeur du paramètre 
    *
    * @param	string	Nom du paramètre demandé (de la table <b>parameters</b>)
    * @return	string  Valeur
    */
   public function GetParamValue($Param)
     {
     $Sql = "SELECT value FROM parameters WHERE name='$Param'";
     $this->Query($Sql);
     $Value = mysql_result( $this->DBQUERY ,0, "value");

     return($Value);
     }
	
// fonction de récupération des donnees
  /**
   * Obsolète
   *
   */
	
	public function db_fetch_row () {
		
		if($this->DBENGINE == "MYSQL") {
		
		return @mysql_fetch_row($this->DBQUERY);
		
		
		}elseif($this->DBENGINE == "SQLSERVER")
		{
			return @mssql_fetch_row($this->DBQUERY);
			
		}elseif ($this->DBENGINE == "POSTGRESQL") {
			
			return @pg_fetch_row($this->DBQUERY);
			
			
		}elseif ($this->DBENGINE == "ORACLE") {
			
			
			//pas encore de code
			
			
		}else {
		
			}
		
		
	}
	
	
  /**
   * Obsolète
   *
   */
   public function db_fetch_object () {
		
	   	if($this->DBENGINE == "MYSQL") {
		
		 return mysql_fetch_object($this->DBQUERY);
		
		
		}elseif($this->DBENGINE == "SQLSERVER")
		{
			return @mssql_fetch_object($this->DBQUERY);
			
		}elseif ($this->DBENGINE == "POSTGRESQL") {
			
			return @pg_fetch_object($this->DBQUERY);
			
		}elseif ($this->DBENGINE == "ORACLE") {
			
			
			// pas encore de code
			
			
		}else {
		
			}
		
	}
	
  /**
   * Obsolète
   *
   */
		public function fetch_array ()	{
			
		if($this->DBENGINE == "MYSQL") {
		
		 return @mysql_fetch_array($this->DBQUERY);
		
		}elseif($this->DBENGINE == "SQLSERVER")
		{
			return @mssql_fetch_array($this->DBQUERY);
			
		}elseif ($this->DBENGINE == "POSTGRESQL") {
			
			return @pg_fetch_array($this->DBQUERY);
			
			
		}elseif ($this->DBENGINE == "ORACLE") {
			
			// pas encore de code
			
		}
			
	}
	// compte du nombre de resultat de la requete
  /**
   * Obsolète
   *
   */
	public function db_nb_result ()	{
				
		if($this->DBENGINE == "MYSQL") {
		
		 return mysql_num_rows($this->DBQUERY);
		
		}elseif($this->DBENGINE == "SQLSERVER")	{
			
			return @mssql_num_rows($this->DBQUERY);
			
		}elseif ($this->DBENGINE == "POSTGRESQL") {
			
			return @pg_num_rows($this->DBQUERY);
			
		}elseif ($this->DBENGINE == "ORACLE") {
			
			//pas encore de code
			
		}
		
		
	}
	
	
  /**
  *  Fermeture de la connexion
  *
  * Ferme la connection
  * @param 	void 	aucun
  * @return	void	aucun
  */
  public function Close()
    {
    switch ($this->DBENGINE)
      {
      case  "MYSQL":
        @mysql_close($this->DBCONNEXION);
        break;

      case  "SQLSERVER":
        @mssql_close($this->DBCONNEXION);
        break;

      case  "POSTGRESQL":
        @pg_close($this->DBCONNEXION);
        break;
      
      default:
	break;
      }
    }
} // Fin de l'objet

?>
