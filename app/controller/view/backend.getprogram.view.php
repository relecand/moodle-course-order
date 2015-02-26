<?php 

/**
 * backend.getprogram.view.php
 * Unterseite zum Auslesen des Stundenplanes
 * @author Robert Leonhardt
 */

## Zeitpunkt des letzten auslesens anhand einer Status-Datei ermitteln
$lastTimetableAccess = THTimetableUtil::GetLastAccess(); #THTimetableUtil::SetLastAccess( false, 42, 42314 );

## prüfen, ob Stundenplan ausgelesen wurde
if ( $lastTimetableAccess == 0 ){
	## Inhalt festlegen
	$content = [ 'status.message'   => HTMLUtil::Snippet( 'content.backend.getprogram.status.null.html' ),
				 'dl.button.text'   => 'Auslesen des Stundenplans beginnen (Schritt 1/3)',
				 'dl.button.action' => 'startdl' ];
} else {
	## Falls die gefundenen, bereits ausgelesenen Kurse nicht dem aktuellen Semester entsprechend, dies melden
	if ( $lastTimetableAccess[ 'semester' ] != ACTUAL_SEMESTER ){
		## Inhalt festlegen
		$content = [ 'status.message'   => HTMLUtil::Snippet( 'content.backend.getprogram.status.deprecated.html' ),
				     'dl.button.text'   => 'Auslesen des AKTUELLEN Stundenplans beginnen (Schritt 1/3)',
				     'dl.button.action' => 'startdl' ];
	} else {
		## Prüfen, ob bisher nur die Seminargruppen ausgelesen wurden
		if ( $lastTimetableAccess[ 'status' ] == 1 ){
			## Inhalt festlegen
			$content = [ 'dl.button.text'   => 'Auslesen der einzelnen Seminargruppenpläne fortsetzen (Schritt 2/3)',
						 'dl.button.action' => 'startdl' ];
		} elseif ( $lastTimetableAccess[ 'status' ] == 2 ){
			## Fall angeklickt, Stundenplandaten übernehmen
			if ( Request::DATA( 'apply', false ) ){
				Request::Send( 'applytimetabledata', Request::SEND_DO );
			}

			## Inhalt festlegen
			$content = [ 'dl.button.text'   => 'Auslesen abgeschlossen, hier klicken, um neuen Stundenplan in die Datenbanken zu übernehmen (Schritt 3/3)',
						 'dl.button.action' => 'apply' ];
		} elseif ( $lastTimetableAccess[ 'status' ] == 3 ){
			## Inhalt festlegen
			$content = [ 'status.message'   => HTMLUtil::Snippet( 'content.backend.getprogram.status.success.html' ),
						 'dl.button.text'   => 'Aktualisierung abgeschlossen, hier klicken, um Daten zu löschen',
						 'dl.button.action' => 'reset' ];
		}
	}
	## Status prüfen
	#var_dump( $lastTimetableAccess ); exit;
}

## Seitentitel setzen
$HTML -> setTitle( 'Administration - Stundenplan auslesen' );

## Inhalt zur Ausgabe hinzufügen
$content[ 'site' ] = HTMLUtil::Snippet( 'content.backend.getprogram.html', $content );

## Javascript laden
$HTML -> includeHeadFile( 'theme/js/jq.js' );
$HTML -> includeHeadFile( 'theme/js/site.backend.js' );

?>