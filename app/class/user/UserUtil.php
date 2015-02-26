<?php 

/**
 * UserUtil.php
 * Klasse mit Methoden zur Benutzerverwaltung
 * @author Robert Leonhardt
 */
abstract class UserUtil
{
    /**
     * Methode zum Prüfen, ob Benutzer in der eigenen Datenbank (NICHT! LDAP!) existiert
     * @return bool
     * @access public
     */
    public static function ExistsInDatabase( $name )
    {
      ## Prüfen, ob Datensatz unter dem Namen existiert und Ergebnis zurückgeben
      return (bool)UserDB::GetData( $name );
    }

    /**
     * Methode zum Ermitteln, ob Benutzer Adminrechte hat
     * @return bool
     * @access public
     */
    public static function IsAdmin( $name )
    {
      ## Benutzerdaten ermitteln
      $data = UserDB::GetData( $name );

      ## Exception, wenn Daten nicht vorhanden (Adminstatus sollte nicht abgefragt werden können, wenn Benutzer nicht eingeloggt ist)
      if ( !$data ){
        throw new UserUtilException( 'Error while receiving permission status', 1002 );
      }

      ## Ergebnis zurückliefern
      return (bool)$data['admin'];
    }

    /**
     * Methode zum Ermitteln der Benutzer-ID
     * @param  string $name
     * @return mixed
     * @access public
     */
    public static function GetID( $name )
    {
      ## Benutzerdaten ermitteln
      $data = UserDB::GetData( $name );

      ## Je nachdem, ob Benutzer existiert, Ergebnis zurückliefern
      return $data ? (int)$data['id'] : 0;
    }

    /**
     * Methode zum Ermitteln von Benutzerdaten anhand der BenutzerID
     * @param  int $id
     * @param  bool $required
     * @return mixed
     * @access public
     */
    public static function GetDataById( $id, $required = true )
    {
      ## Daten ermitteln
      $data = UserDB::GetDataById( intval( $id ) );

      ## Exception, wenn keine Daten vorhanden (User nicht vorhanden), aber benötigt
      if ( !$data and $required ){
        throw new UserUtilException( 'Required user data not found', 1003 );
      }

      ## Daten zurückliefern
      return $data;
    }

    /**
     * Methode zum Registrieren eines neuen Benutzers
     * @param  string $name
     * @param  bool $admin
     * @param  bool $protected
     * @return int
     * @access public
     */
    public static function Create( $name, $admin = false, $protected = false )
    {
      ## Exception, wenn Benutzername bereits existiert
      if ( self::ExistsInDatabase( $name ) ){
        throw new UserUtilException( 'User already exists', 1001 );
      }

      ## Benutzer in Datenbank anlegen
      UserDB::Register( $name, (bool)$admin, (bool)$protected );
    }
}

/**
 * Exception-Klasse
 */
class UserUtilException extends Exception {}

?>