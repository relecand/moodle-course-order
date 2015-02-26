<?php 

/**
 * Base64.php
 * Klasse zum (De-)Kodieren von Strings
 * @author Robert Leonhardt
 */
abstract class Base64
{
  
  /**
   * Methode zum Kodieren eines Strings
   * @param string $string
   * @param string $password
   * @return string
   * @access public
   */
  public static function Encode( $string, $password = null )
  {
    ## String bearbeiten, falls Passwort vorhanden
    if ( isset( $password ) ){
      ## Passwort MUSS string sein
      $password = (string)$password;

      ## Länge des Strings ermitteln
      $length = array(
        'string'   => strlen( $string ),
        'password' => strlen( $password )
      );

      ## der zu verschlüsselnde String
      $encode = '';

      ## Schleife zum Durchgehen aller Zeichen
      for ( $count = 0; $count < $length['string']; $count++ )
      {
        ## Zeichen ermitteln
        $char = substr( $string, $count, 1 );

        ## Key ermitteln
        $key = substr( $password, ( $count % $length['password'] ) - 1, 1 );

        ## Zeichen austauschen und dem zu kodierenden String hinzufügen
        $encode .= chr( ord( $char ) + ord( $key ) );
      }
    } else {
      ## zu koderiender String ist der eingangs angegebene String
      $encode = $string;
    }

    ## endgültigen String kodieren und zurückliefern
    return base64_encode( $encode );
  }

  /**
   * Methode zum Dekodieren eines Strings
   * @param string $string
   * @param string $password
   * @return string
   * @access public
   */
  public static function Decode( $string, $password = null )
  {
    ## String dekodieren
    $string = base64_decode( $string );

    ## String bearbeiten, falls Passwort vorhanden
    if ( isset( $password ) ){
      ## Passwort MUSS string sein
      $password = (string)$password;

      ## Länge des Strings ermitteln
      $length = array(
        'string'   => strlen( $string ),
        'password' => strlen( $password )
      );

      ## der entschlüsselte String
      $decode = '';

      ## Schleife zum Durchgehen aller Zeichen
      for ( $count = 0; $count < $length['string']; $count++ )
      {
        ## Zeichen ermitteln
        $char = substr( $string, $count, 1 );

        ## Key ermitteln
        $key = substr( $password, ( $count % $length['password'] ) - 1, 1 );

        ## Zeichen austauschen und dem zu dekodierenden String hinzufügen
        $decode .= chr( ord( $char ) - ord( $key ) );
      }
    } else {
      ## zu dekoderiender String ist der eingangs angegebene String
      $decode = $string;
    }

    ## String zurückliefern
    return $decode;
  }

}

?>