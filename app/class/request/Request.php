<?php 

/**
 * Request.php
 * (Eltern-)Klasse zum Ermitteln von Requestdaten
 * @author Robert Leonhardt
 */
abstract class Request
{
	/**
	 * Konstanten
	 */
	const GET    = 1;
	const POST   = 2;
	const SERVER = 4;
	const COOKIE = 8;
	const FILES  = 16;

	/**
   	 * Konstanten (Weiterleitung)
   	 */
  	const SEND_VIEW = 1;
  	const SEND_DO   = 2;
  	const SEND_EXT  = 3;

	/**
	 * "stringbare" Datentypen
	 * @access private
	 */
	private static $ValidDataTypes = [ 'GET', 'POST', 'SERVER', 'DATA', 'COOKIE' ];

	/**
	 * Methode zum (ungeschützen, (PROTECTED!)) Ermitteln von Daten
	 * @param string $name
	 * @param int    $type
	 * return bool
	 * @access protected
	 */
	protected static function GetData( $name, $type = null )
	{
		## Array mit auszuwertenden Daten
		$data = [];

		## Daten je nach Typ zusammenstellen
		switch ( $type )
		{
			## Nur $_GET-Daten
			case self::GET    : $data = $_GET;    break;
			## Nur $_POST-Daten
			case self::POST   : $data = $_POST;   break;
			## Nur $_SERVER-Daten
			case self::SERVER : $data = $_SERVER; break;
			## Nur $_COOKIE-Daten
			case self::COOKIE : $data = $_COOKIE; break;
			## Nur $_FILES-Daten
			case self::FILES  : $data = $_FILES;  break;
			## $_GET- und $_POST-Daten (standard)
			default :
			case self::GET ^ self::POST : $data = array_merge( $_POST, $_GET ); break;
		}

		## Ergebnis zurückliefern
		return isset( $data[ $name ] ) ? $data[ $name ] : null;
	}

	/**
	 * Methode zum Datenzugriff außerhalb des Klassenkontexts
	 * Eigentlich: Request::TYPE( $name, $alternative = null, $required = false )
	 * ( @param string $name
	 *   @param mixed  $alternative
	 *   @param bool   $required )
	 * -
	 * @param string $type
	 * @param array  $args
	 * @return mixed
	 * @access public
	 */
	public static function __callStatic( $type, array $args )
	{
		## Fehler ausgeben, wenn Datentyp nicht gültig ist -> wird als fehlerhafter Methodenaufruf interpretiert
		if ( !in_array( $type, self::$ValidDataTypes ) ){
			trigger_error( 'Call to undefined method Request::' . $type . '()', E_USER_ERROR );
		}

		## Fehler ausgeben, wenn keine weiteren Parameter übergeben wurden -> wird als Parameterfehler interpretiert
		if ( empty( $args ) ){
			trigger_error( $type . '() expects at least 1 parameter, 0 given', E_USER_WARNING );
			return false;
		}

		## Zumindest die gesuchte Datenbezeichnung liegt vor ..
		$name 		 = $args[0];
		$alternative = isset( $args[1] ) ? htmlentities( $args[1] ) : null;
		$required    = isset( $args[2] ) ? (bool)$args[2] : false;

		## Daten ermitteln
		switch ( $type )
		{
			## Request::GET(..)
			case 'GET'    : $data = self::GetData( $name, self::GET ); 				break;
			## Request::POST(..)
			case 'POST'   : $data = self::GetData( $name, self::POST ); 			break;
			## Request::SERVER(..)
			case 'SERVER' : $data = self::GetData( $name, self::SERVER );           break;
			## Request::DATA(..)
			case 'DATA'   : $data = self::GetData( $name, self::GET ^ self::POST ); break;
			## Request::COOKIE(..)
			case 'COOKIE' : $data = self::GetData( $name, self::COOKIE );			break;
		}

		## Exception, wenn Daten gefordert ($required), aber nicht existent sind
		if ( $data == null and $required )
		{
			throw new RequestException( 'Required request data not found', 1001 );
		}

		## Rückgabe
		return ( $data == null ) ? $alternative : $data;
	}

	/**
	 * Methode zur Weiterleitung
	 * @param string $location
	 * @param int $type
	 * @param array(mixed) $data
	 * @param string $hash
	 * @access public
	 */
	 public static function Send( $location = null, $type = 1, array $data = array(), $hash = null )
	 {
	  	## Weiterleitungstyp verarbeiten
	   	switch ( $type )
	    {
	      case self::SEND_VIEW: default:
	        $url = SELF_URL . '?view=' . urlencode( $location );
	        break;
	      case self::SEND_DO:
	        $url = SELF_URL . '?do=' . urlencode( $location );
	        break;
	      case self::SEND_EXT:
	        $url = 'http://' . urlencode( $location );
	        break;
	    }

	    ## ggf. Daten via GET anhängen
	    foreach ( $data as $key => $value )
	    {
	      $url .= '&' . urlencode( $key ) . '=' . urlencode( $value );
	    }

	    ## ggf. Hash anhängen
	    if ( $hash ){
	    	$url .= htmlentities( $hash );
	    }

	    ## Weiterleiten
	    Header( 'Location: ' . $url );

	    ## Weitere Bearbeitung beenden
	    exit;
	  }
}

/**
 * Exception-Klasse
 */
class RequestException extends Exception {}

?>