<?php 
require_once( CONFIG . 'THTimetable.config.php' );

/**
 * Seite zum Laden des Stundenplans
 * @author Robert Leonhardt
 */

## Zur Startseite (Bestellseite), wenn Benutzer kein Admin ist
if ( !$Session -> isAdmin() ){
  Request::Send( 'main' );
}

## Nur ausführen, wenn Parameter übergeben (anderfalls spielt jemand mit dem HTML-Code)
if ( Request::DATA( 'ttr', false ) ){

	/*

	Struktur
	---'timestamp'    => TIME_NOW, 
	---'semester'     => ACTUAL_SEMESTER,
	'status'  	   => (int)$status,
	'sg'		   => $sg,
	'sgFound'	   => (int)$sgFound,
	'sgActual' 	   => (int)$sgActual,
	'sgDownloaded' => (int)$sgDownloaded,
	'teacher'      => (int)$teacher,
	'courses'      => (int)$courses

	*/

	## Timetable-Objekt
	$THTT = new THTimetable;

	## Statusdatei abrufen
	$lastTimetableAccess = THTimetableUtil::GetLastAccess();

	## Pfad für Seminargruppen-Dateien
	$sgFileDir = THTT_DATA . ACTUAL_SEMESTER . '/';

	## Wenn Status nicht verfügbar (Stundenplan nicht ausgelesen), Statusdatei anlegen anlegen
	if ( $lastTimetableAccess == 0 ){
		## Statusdatei anlegen
		THTimetableUtil::SetLastAccess();

		## .. und gleich auslesen
		$lastTimetableAccess = THTimetableUtil::GetLastAccess();
	}


	## Array-Namen verkürzen ..
	$lta = $lastTimetableAccess;	


	## Fall Datei existiert aber veraltet ist ..
	if ( $lta[ 'semester' ] != ACTUAL_SEMESTER ){
		## Datei in ihr Verzeichnis kopieren (Backup, just in case)
		copy( THTT_DATA . 'last.access', THTT_DATA . $lta[ 'semester' ] . '/last.access.BACKUP' );

		## Urpsrungsdatei löschen (Platz für Neues schaffen)
		unlink( THTT_DATA . 'last.access' );

		## Statusdatei anlegen
		THTimetableUtil::SetLastAccess();

		## .. und gleich auslesen
		$lta = THTimetableUtil::GetLastAccess();
	}


	## Je nach Status agieren
	switch ( $lta[ 'status' ] )
	{
		## Standardfall: Es ist nocht NICHTS ausgelesen
		case 0:
		{
			## Seminargruppen auslesen
			$seminarGroups = $THTT -> getAllSeminarGroups();

			## Statusdaten anpassen
			THTimetableUtil::SetLastAccess( 1, $seminarGroups, count( $seminarGroups ) );

			## Verzeichnis anlegen (falls nicht vorhanden)
			if ( !is_dir( $sgFileDir ) ){
				mkdir( $sgFileDir );
			}
		}
		break;

		## Seminargruppen sind ausgelesen, nun die jeweiligen Stundenpläne auslesen
		case 1:
		{
			## numerierte Version des Seminargruppen-Array erstellen 
			$seminarGroupsNumbered = array_keys( $lta[ 'sg' ] );

			## Stundenplan der aktuellen Seminargruppe runterladen
			$fileStatus = file_put_contents( $sgFileDir . (int)$lta[ 'sgActual' ] . '.sg', serialize( $THTT -> getTimetable( $seminarGroupsNumbered[ $lta[ 'sgActual' ] ] ) ) );

			## Exception, wenn Datei nicht erstellt
			if ( $fileStatus === false ){
				throw new Exception( 'Cannot create file' );
			}

			## Prüfen, ob letzte zu ladende Seminargruppe fertig ist
			if ( $lta[ 'sgActual' ] >= ( $lta[ 'sgFound' ] - 1 ) ){
				## Status setzen
				THTimetableUtil::SetLastAccess( 2, $lta[ 'sg' ], $lta[ 'sgFound' ], $lta[ 'sgActual' ] );
			} else {
				THTimetableUtil::SetLastAccess( 1, $lta[ 'sg' ], $lta[ 'sgFound' ], $lta[ 'sgActual' ] + 1 );
			}
		}
		break;

		## Post-Processing: Verarbeiten der Stundenpläne etc.
		case 2:
		{
			## some magic
			##file_put_contents( THTT_DATA . 'FERTIG', 'Fertig!' );

			## Status setzen
			THTimetableUtil::SetLastAccess( 3, $lta[ 'sg' ], $lta[ 'sgFound' ], $lta[ 'sgActual' ] );
		}
	}

	## aktuellen Status ausgeben
	echo json_encode( THTimetableUtil::GetLastAccess() );
}

## Exitbefehl vermeidet Fehler
exit;

?>