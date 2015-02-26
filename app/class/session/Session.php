<?php 
require_once( CONFIG . 'session.config.php' );

/**
 * Session.class.php
 * Klasse zur Verarbeitung der Session
 * @author Robert Leonhardt
 */
class Session
{

  /**
   * SessionID
   * @access private
   */
  private $id;

  /**
   * Der Session zubehöriger Benutzer
   * @access private
   */
  private $user;

  /**
   * Ablaufzeitpunkt der Session
   * @access private
   */
  private $expire;
 
  /**
   * Konstruktormethode zum Intialisieren der Session
   * @access public
   */
  public function __construct()
  {
    ## Session-ID ermitteln (cookie) oder generieren
    $this -> id     = Cookie::Get( SESSION_COOKIE, self::GenerateSessionID() );
    ## Benutzer ist vorerst nicht eingeloggt
    $this -> user   = null;
    ## Session ist vorerst zeitlich neuwertig
    $this -> expire = TIME_NOW + SESSION_DURATION;

    ## Sessiondaten aus Datenbank auslesen
    $data = SessionDB::GetData( $this -> id );

    ## Falls die Daten existieren, diese übernehmen; Andernfalls neuen Datensatz erstellen
    if ( $data ){
      ## Benutzer übernehmen
      $this -> user   = $data['user'];
      ## Ablaufzeitpunkt übernehmen
      $this -> expire = $data['expire'];
    } else {
      ## Session anmelden
      SessionDB::Register( $this -> id );
    }

    ## Prüfen, ob Session abgelaufen ist
    if ( $this -> expire < TIME_NOW ){
      ## Benutzer "zurücksetzen"
      $this -> user = null;
    }

    ## Datenbankeintrag aktualisieren
    SessionDB::Update( $this -> id, $this -> user );

    ## Session-Cookie setzen
    Cookie::Set( SESSION_COOKIE, $this -> id, SESSION_DURATION );
  }

  /**
   * Methode zum Ermitteln des Benutzers
   * @return int
   * @access public
   */
  public function getUser()
  {
    ## Benutzernamen zurückliefern
    return $this -> user;
  }

  /**
   * Methode zum Prüfen, ob Benutzer eingeloggt ist
   * @return bool
   * @access public
   */
  public function isLoggedIn()
  {
    ## Prüfen, ob Benutzer vorhanden und Ergebnis zurückliefern
    return (bool)$this -> getUser();
  }

  /**
   * Methode zum Prüfen, ob Benutzer Admin ist
   * @return bool
   * @access public
   */
  public function isAdmin()
  {
    ## Benutzer ist kein Admin, wenn nicht eingeloggt
    if ( !$this -> isLoggedIn() ){
      return false;
    }

    ## Benutzerdaten ermitteln
    $data = UserUtil::GetDataById( $this -> user );

    ## Status zurückgeben
    return $data['admin'];
  }

  /**
   * Methode zum Ändern des Benutzers
   * @param int $user
   * @return $this (chaining)
   * @access public
   */
  public function changeUser( $user )
  {
    ## Benutzer übernehmen
    $this -> user   = intval( $user );
    ## Ablaufzeitpunkt aktualisieren
    $this -> expire = TIME_NOW + SESSION_DURATION;

    ## Session Updaten
    SessionDB::Update( $this -> id, $this -> user );

    ## return $this (chaining)
    return $this;
  }

  /**
   * Methode zum Verwerfen einer Session (Session-Cookie und -datensatz löschen)
   * @access public
   */
  public function destroy()
  {
    ## Benutzer bis zum Ende der Skriptausführung auf null setzen
    $this -> user = null;

    ## Datensatz löschen
    SessionDB::DeleteFromId( $this -> id );

    ## Session-Cookie löschen
    Cookie::Remove( SESSION_COOKIE );
  }

  /**
   * Methode zum Generieren einer SessionID
   * @return string
   * @access private
   */
  private static function GenerateSessionID()
  {
    ## String errechnen, Timestamp anfügen und zurückliefern
    return TIME_NOW . '.' . str_shuffle( sha1( TIME_NOW ) );
  }

}

?>