<?php 

/**
 * global.php
 * Global verfügbare Datei (siehe .user.ini) mit wichtigen Pfadangaben, Autoloadern, etc.
 * @author Robert Leonhardt
 */

## Proxy-Cache umgehen
header("Cache-Control: no store" );


## Eigene URL
define( 'SELF_URL', 'http://cc.elearning.dev.com/' );


## Anwendungsverzeichnis
define( 'ROOT',   	  dirname( __FILE__ ) . '/' );
## Arbeitsverzeichnis
define( 'SYSTEM',     ROOT   . 'app/' );
## Konfigurationsverzeichnis
define( 'CONFIG', 	  SYSTEM . 'configuration/' );
## Controller-Verzeichnis
define( 'CONTROLLER', SYSTEM . 'controller/' );
## Template-Verzeichnis
define( 'HTML',   	  SYSTEM . 'html/' );


## Aktuelles Uhrzeit
define( 'TIME_NOW',   time() );


## Standard-Semesterbezeichnung
## Von Anfang Juni bis Ende November standardmäßig WiSe, sonst SoSe
define( 'ACTUAL_SEMESTER', ( ( date( 'n' ) > 5 and date( 'n' ) < 12 ) ? 'Wi' : 'So' ) . 'Se' . date( 'y' ) );


## Autoloader laden
require_once( SYSTEM . 'function/autoload.php' );
## Autoloader registrieren
spl_autoload_register( function( $class ){
  ## Autoload-Funktion aufrufen
  require_once( autoload( $class ) );
} );

?>