<?php 

/**
 * backend.view.php
 * Seite mit Administrationsmenü und Kursantragsbeantwortung
 * @author Robert Leonhardt
 */

## Zur Startseite, wenn Benutzer kein Admin ist
if ( !$Session -> isAdmin() ){
  Request::Send( 'main' );
}

## Array mit Inhalten zusammenstellen
$content = [];

## Seite ermitteln
$site = Request::GET( 'site', null );

## Array mit verfügbaren unterseiten (hier können einzelne Seite übergangsweise deaktiviert werden ..)
$availableSites = [ 'courserequests', 'editprogram', 'getprogram', 'edituser', 'editconfig', 'resetdb', 'bugs' ];

## (Sub-)Controller-Pfad erstellen
$subController = CONTROLLER . 'view/backend.' . $site . '.view.php';

## Wenn Seite existiert, laden, sonst zu den Kursanträgen
if ( file_exists( $subController ) and in_array( $site, $availableSites ) ){
	## aktives Menüelement markieren
	$activeMenuElement = $site;

	## Unterseite laden
	require_once( $subController );
} else {
	## Zur Übersichstseite (Kursanträge) leiten
	Request::Send( 'backend', SEND_VIEW, [ 'site' => 'courserequests' ] );
}


## Seite verarbeiten
switch ( $site )
{

	## angefordert: Seite mit Kursanträgen
	case 'courserequests':
	{
		## aktives Menüelement markieren
		$activeMenuElement = 'a1';
	} break;

	## angefordert: Seite mit Curriculum
	case 'editprogram':
	{
		## aktives Menüelement markieren
		$activeMenuElement = 'a2';
	} break;

	## angefordert: Seite mit Optionen zum Auslesen des Stundenplanes
	case 'getprogram':
	{
		## aktives Menüelement markieren
		$activeMenuElement = 'a3';
	} break;

	## angefordert: Seite mit Benutzern
	case 'edituser':
	{
		## aktives Menüelement markieren
		$activeMenuElement = 'a4';
	} break;

	## angefordert: Seite mit Optionen
	case 'editconfig':
	{
		## aktives Menüelement markieren
		$activeMenuElement = 'a5';
	} break;

	## angefordert: Seite mit Optionen für den Testbetrieb (kann anschließend auskommentiert werden ..)
	case 'resetdb':
	{
		## aktives Menüelement markieren
		$activeMenuElement = 'a6';
	} break;

	## angefordert: Seite mit Optionen für den Testbetrieb (kann anschließend auskommentiert werden ..)
	case 'bugs':
	{
		## aktives Menüelement markieren
		$activeMenuElement = 'a7';
	} break;

	## Eingabe manipuliert ..
	default: Request::Send( 'backend', SEND_VIEW, [ 'site' => 'courserequests' ] ); break;
}


## Seitentitel setzen
#$HTML -> setTitle( 'Administration' );

## Inhalte zusammenstellen
$content[ $activeMenuElement ] = 'active';

## Inhalt einfügen
$HTML -> replace( 'body.content', HTMLUtil::Snippet( 'content.backend.main.html', $content ) );

?>