<?php
require_once( CONFIG . 'session.config.php' );

/**
 * SessionDB.class.php
 * Klasse zur Verwaltung der Session-DB-Tabellen
 * @author Robert Leonhardt
 */
abstract class SessionDB
{
  
  /**
   * Array mit ausgelagerten SQL-Anweisungen
   * @access private
   */
  private static $SQL = [
    'session.delete.sid'     => 'delete from session where id = :sid;',
    'session.delete.all'     => 'truncate table session;',
    'session.delete.user'    => 'delete from session where user = :user;',
    'session.delete.expired' => 'delete from session where expire < :time;',
    'session.get'            => 'select * from session where id = :sid;',
    'session.register'       => 'insert into session ( id, user, expire ) values ( :sid, :user, :expire );',
    'session.update'         => 'update session set user = :user, expire = :expire where id = :sid;'
  ];

  /**
   * Methode zum Ermitteln von Sessiondaten
   * @param string $sid
   * @return array(mixed)
   * @access public
   */
  public static function GetData( $sid )
  {
    ## Statementhandler
    $STH = Database::GetPrepared( self::$SQL['session.get'] );

    ## SQL ausführen
    $STH -> execute( array( ':sid' => $sid ) );

    ## Ergebnis zurückliefern
    return $STH -> fetch();
  }

  /**
   * Methode zum Anmelden einer Session (Erstellen des Datensatzes in der DB)
   * @param $string $sid
   * @access public
   */
  public static function Register( $sid )
  {
    ## Statementhandler
    $STH = Database::GetPrepared( self::$SQL['session.register'] );

    ## SQL ausführen und Platzhalter ersetzen
    $STH -> execute( array(
      ':sid'    => $sid,
      ':user'   => null,
      ':expire' => TIME_NOW + SESSION_DURATION
    ) );
  }

  /**
   * Methode zum Aktualisieren einer Session
   * @param string $sid
   * @param int $user
   * @access public
   */
  public static function Update( $sid, $user )
  {
    ## Statementhandler
    $STH = Database::GetPrepared( self::$SQL['session.update'] );

    ## SQL ausführen und Platzhalter ersetzen
    $STH -> execute( array(
      ':sid'    => $sid,
      ':user'   => $user,
      ':expire' => TIME_NOW + SESSION_DURATION
    ) );
  }

  /**
   * Methode zum Löschen einer Session anhand ihrer ID
   * @param string $sid
   * @access public
   */
  public static function DeleteFromId( $sid )
  {
    ## Statementhandler
    $STH = Database::GetPrepared( self::$SQL['session.delete.sid'] );

    ## SQL ausführe
    $STH -> execute( array( ':sid' => $sid ) );
  }

  /**
   * Methode zum Löschen aller abgelaufenen Sessions
   * @access public
   */
  public static function ClearExpiredSessions()
  {
    ## Statementhandler
    $STH = Database::GetPrepared( self::$SQL['session.delete.expired'] );

    ## SQL ausführen
    $STH -> execute( array( ':time' => TIME_NOW ) );
  }

  /**
   * Methode zum Leeren der Tabelle
   * @access public
   */
  public static function ClearAllSessions()
  {
    ## SQL ausführen
    Database::GetInstance() -> query( self::$SQL['session.delete.all'] );
  }

}

?>