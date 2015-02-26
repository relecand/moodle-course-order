<?php 
require_once( CONFIG . 'THLDAP.config.php' );

/**
 * LDAP.php
 * Klasse zum Zugriff auf den TH-LDAP-Dienst
 * @author Robert Leonhardt
 */
class THLDAP
{
    /**
     * LDAP-Connection-Handler
     * @access private
     */
    private $connectionHandler;

    /**
     * Konstruktor zum Herstellen einer Verbindung
     * @access public
     */
    public function __construct()
    {
        ## Herstellen der Verbindung
        $this -> connectionHandler = ldap_connect( THLDAP_SERVER, THLDAP_PORT ); 

        ## Exception, wenn Verbindung fehlschlägt
        if ( !@ldap_bind( $this -> connectionHandler ) ){
            throw new THLDAPException( 'Connection to ' . THLDAP_SERVER . ':' . THLDAP_PORT . ' failed', 1001 );
        }

        ## LDAP-Optionen setzen
        ldap_set_option( $this -> connectionHandler, LDAP_OPT_PROTOCOL_VERSION, 3 );
        ldap_set_option( $this -> connectionHandler, LDAP_OPT_REFERRALS, 0 );
    }

    /**
     * Methode zum Validieren eines Benutzers
     * (Es wird geprüft, ob der TH-Zugang gültig ist)
     * @param  string $username
     * @param  string $password
     * @return bool
     * @access public
     */
    public function validateLogin( $username, $password )
    {
        ## LDAP-Bind ausführen und Ergebnis zurückliefern
        return @ldap_bind( $this -> connectionHandler, str_replace( '{user}', $username, THLDAP_BIND_DN ), $password );
    }

    /**
     * Methode zum Suchen von Einträgen
     * @param  string $base
     * @param  string $filter
     * @return array
     * @access public
     */
    public function search( $base = null, $filter = null )
    {
        ## Root-Basis nehmen, wenn nichts Anderes angegeben
        $base   = isset( $base ) ? $base : THLDAP_PATH_ROOT;

        ## Nach allem suchen, wenn nichts Anderes angegeben
        $filter = isset( $filter ) ? $filter : 'ou=*';

        ## Suche ausführen 
        $search = ldap_search( $this -> connectionHandler, $base, $filter );

        ## Suchergebnis zurückliefern
        return ldap_get_entries( $this -> connectionHandler, $search );
    }

    /**
     * Methode zum Prüfen, ob Benutzer ein Mitarbeiter ist
     * @param  string $user
     * @return bool
     * @access public
     */
    public function isCoworker( $user )
    {
        ## Array mit allen Mitarbeitern zusammenstellen (unsortiert)
        $data = $this -> search( THLDAP_PATH_COWORKER, 'ou=*' );

        ## die folgende separat eingeklammerte Methode, das Array zu durchsuchen, ist etwas unsauber, tut aber seinen Zweck
        ## und kann ggf. später durch einen passenderen LDAP-Befehl ersetzt werden, um die Performance zu setiegern.
        {
            ## resultierendes Array zu einem durchsuchbaren String konvertieren
            $data = serialize( $data );

            ## String nach Benutzernamen durchsuchen und Ergebnis zurückliefern
            return preg_match( '/' . $user . '/', $data );
        }
    }
}

/**
 * Exception-Klasse
 */
class THLDAPException extends Exception {}

?>