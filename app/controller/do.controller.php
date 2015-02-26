<?php 

/**
 * main.controller.php
 * Haupt-DO-Controller
 * @auhtor Robert Leonhardt
 */

## Angeforderte Aktion ermitteln
define( 'DO_REQUESTED', Request::DATA( 'do', false ) );
## Angeforderte Aktion als Pfad
define( 'DO_REQUESTED_PATH', SYSTEM . 'controller/do/' . DO_REQUESTED . '.do.php' );


## Wenn Anfrage von unangemeldetem Besuch, der sich nicht zufällig anmelden will oder die Datei nicht existiert -> auf Loginseite leiten
if ( ( $Session -> getUser() == null and DO_REQUESTED != 'login' ) or !file_exists( DO_REQUESTED_PATH ) ){
  Request::Send( 'order' );
}


## Aktionsdatei existiert; selbige laden
require_once( DO_REQUESTED_PATH );

## Sollte keine spezifische Weiterleitung vorliegen, dann zur Startseite weiterleiten
Request::Send( null, Request::SEND_VIEW, array( 'status' => 1 ) );

?>