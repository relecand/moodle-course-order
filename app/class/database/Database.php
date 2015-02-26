<?php 
require_once( CONFIG . 'database.config.php' );

/**
 * Database.php
 * Klasse zum Ermitteln einer Datenbankverbindung
 * @author Robert Leonhardt
 */
class Database
{
  
  /**
   * Datenbankinstanz
   * @var PDO
   * @access private
   */
  private static $Instance = null;

  /**
   * Methode zum "Holen" der Instanz
   * @return PDO
   * @access public
   */
  public static function GetInstance()
  {
    ## Neue Instanz erstellen, falls noch keine existiert
    if ( !isset( self::$Instance ) ){
      ## Instanz erstellen
      self::$Instance = new PDO( DB_PDO_CONNECTIONSTRING, DB_USER, DB_PASS, array( PDO::MYSQL_ATTR_INIT_COMMAND => "set names 'utf8'" ) );

      ## Standard-Fehlermethode setzen
      self::$Instance -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

      ## Standard-Fetchmode setzen
      self::$Instance -> setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
    }

    ## Instanz zurückliefern
    return self::$Instance;
  }

  /**
   * Methode zum Ermitteln eines vorbereiteten Statements
   * @param string $sql
   * @return PDOStatementHandler
   * @access private
   */
  public static function GetPrepared( $sql )
  {
    ## Query vorbereiten und zurückliefern
    return self::GetInstance() -> prepare( $sql );
  }

}

?>