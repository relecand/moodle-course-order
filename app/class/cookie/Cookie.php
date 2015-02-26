<?php 
require_once( CONFIG . 'cookie.config.php' );

/**
 * Cookie.php
 * Klasse zur Cookie-Verwaltung
 * @author Robert Leonhardt
 */
abstract class Cookie
{
    /**
     * Array mit Regex-Patterns
     * @var    array( mixed )
     * @access private
     */
    private static $Regex = [ 'name'  => '/^[a-z0-9\.]+$/si' ];

    /**
     * Methode zum Anlegen von Cookies
     * @param  string $name
     * @param  mixed  $value
     * @param  int    $expire
     * @return bool
     * @access public
     */
    public static function Set( $name, $value = null, $lifetime = null )
    {
        ## Exception, wenn Cookie-Name ungültig
        if ( !preg_match( self::$Regex['name'], $name ) ){
            throw new CookieException( 'Invalid cookie name. (' . $name . ')', 1001 );
        }

        ## TODO! Inhaltsvalidierung

        ## Cookie-Lebensdauer ermitteln
        $expire = TIME_NOW + ( (int)$lifetime > 0 ? $lifetime : COOKIE_DEFAULT_LIFETIME );

        ## Cookie setzen
        setcookie( COOKIE_NAME_APPEND . $name, $value, $expire );

        ## TODO! Cookie auf Existenz/Inhalt prüfen und Ergebnis zurückliefern
        return true;
    }

    /**
     * Methode zum Auslesen eines Cookies
     * @param  string $name
     * @param  mixed $alternative
     * @return mixed
     * @access public
     */
    public static function Get( $name, $alternative = null )
    {
        ## Cookie auslesen und zurückliefern
        return Request::COOKIE( COOKIE_NAME_APPEND . $name, $alternative );
    }

    /**
     * Methode zum Löschen eines Cookies
     * @param  string $name
     * @access public
     */
    public static function Remove( $name )
    {
        ## Cookie mit Negativer Laufzeit erstellen
        self::Set( $name, null, 0 - TIME_NOW - 1 );
    }
}

/**
 * Exception-Klasse
 */
class CookieException extends Exception {}

?>