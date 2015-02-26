<?php 

/**
 * UserDB.php
 * Klasse zur Benutzer-Datenbankverwaltung
 * @author Robert Leonhardt
 */
abstract class UserDB
{
  /**
   * Array mit SQL-Queries
   * @var    array(string)
   * @access private
   */
  private static $SQL = [
    'user.get'              => 'select * from user where name = :name;',
    'user.getById'          => 'select * from user where id = :id;',
    'user.register'         => 'insert into user ( name, admin, protected ) values ( :name, :admin, :protected );',
    'user.delete.nonadmins' => 'delete from user where admin = 0;'
  ];

  /**
   * Methode zum Ermitteln von Benutzerdaten
   * @param  string $name
   * @return array(mixed)
   * @access public
   */    
  public static function GetData( $name )
  {
    ## Statementhandler
    $STH = Database::GetPrepared( self::$SQL['user.get'] );

    ## SQL ausführen
    $STH -> execute( array( ':name' => $name ) );

    ## Ergebnis zurückliefern
    return $STH -> fetch();
  }

  /**
   * Methode zum Ermitteln von Benutzerdaten anhand der ID
   * @param  int $id
   * @return array(mixed)
   * @access public
   */
  public static function GetDataById( $id )
  {
    ## Statementhandler
    $STH = Database::GetPrepared( self::$SQL['user.getById'] );

    ## SQL ausführen
    $STH -> execute( array( ':id' => $id ) );

    ## Ergebnis zurückliefern
    return $STH -> fetch();
  }

  /**
   * Methode zum Erstellen von Benutzern
   * @param  string $name
   * @param  bool $admin
   * @param  bool $protected
   * @access public
   */
  public static function Register( $name, $admin = false, $protected = false )
  {
    ## Statementhandler
    $STH = Database::GetPrepared( self::$SQL['user.register'] );

    ## SQL ausführen und Platzhalter ersetzen
    $STH -> execute( [
      ':name'      => $name,
      ':admin'     => (bool)$admin,
      ':protected' => (bool)$protected
    ] );
  }

  /**
   * Methode zum Löschen von Nichtadmins
   * @access public
   */
  public static function DeleteAllNonadmins()
  {
    ## SQL ausführen
    Database::GetInstance() -> query( self::$SQL['user.delete.nonadmins'] );
  }
}

/**
 * Exception-Klasse
 */
class UserDBException extends Exception {}

?>