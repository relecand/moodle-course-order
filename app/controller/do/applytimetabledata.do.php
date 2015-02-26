<?php 
require_once( CONFIG . 'THTimetable.config.php' );

/**
 * Seite zum Verarbeiten der Stundenplandaten
 * @author Robert Leonhardt
 */

## Zur Startseite (Bestellseite), wenn Benutzer kein Admin ist
if ( !$Session -> isAdmin() ){
  Request::Send( 'main' );
}

## Statusdatei abrufen
$lastTimetableAccess = THTimetableUtil::GetLastAccess();

## Name verkürzen
$lta = $lastTimetableAccess;

## Zurück, wenn Status nicht passt
if ( $lta[ 'status' ] != 2 ){
	Request::Send( 'backend', SEND_VIEW, [ 'site' => 'getprogram' ] );
}

## Array mit Kursen für die weiterführende Verarbeitung
$courses = [ 'bySG' => [], 'byTeacher' => [] ];

## Seminargruppen auslesen
$seminarGroupNames = array_keys( $lta[ 'sg' ] );

## Array mit Kursen nach Seminargruppen geordnet durchgehen
for ( $i = 0; $i < count( glob( THTT_DATA . ACTUAL_SEMESTER . '/*.sg' ) ); $i++ )
{
	## Kurse zusammenführen
	$courses[ 'bySG' ] = array_merge( $courses[ 'bySG' ], [ $seminarGroupNames[ $i ] => unserialize( file_get_contents( THTT_DATA . ACTUAL_SEMESTER . '/' . $i . '.sg' ) ) ] );
}

## Array mit Kursen nach Dozenten geordnet // Alle nach Seminargruppen geordneten Daten durchgehen
foreach ( $courses[ 'bySG' ] as $SG => $coursesBySG )
{
	## nicht benötigte Arrayelemente entfernen, falls vorhanden
	if ( isset( $coursesBySG[ 'stundenplan' ] ) ){
		unset( $coursesBySG[ 'stundenplan' ] );
	}
	
	## Alle Wochentage durchgehen
	foreach ( $coursesBySG as $day )
	{
		## Alle Kurse durchgehen
		foreach ( $day as $course )
		{
			
			## Array mit gültigen Seminargruppen erstellen
			if ( !isset( $course[ 'validsg' ] ) ){
				$course[ 'validsg' ] = [];
			}

			## Alle im Kurs angegebenen Dozenten durchgehen
			foreach ( $course[ 'teacher' ] as $teacher )
			{
				## Dozent in Hauptarray anlegen, sofern dieser noch nicht existiert
				if ( !isset( $courses[ 'byTeacher' ][ $teacher ] ) ){
					## Grundarray für Dozent anlegen
					$courses[ 'byTeacher' ][ $teacher ] = [];
				}

				## Andere Dozenten aus Array werfen
				$course[ 'teacher' ] = $teacher;

				## Hilfsvariable für Kurse mit gleichen Namen
				$coursesWithSameName = [];

				## Alle Kurse des Dozenten durchgehen, um doppelte Kurse zu prüfen
				foreach ( $courses[ 'byTeacher' ][ $teacher ] as $existingCourseIndex => $existingCourse )
				{
					## prüfen, ob Kursname gleich ist
					if ( $course[ 'name' ] == $existingCourse[ 'name' ] ){
						## Kurs existiert bereits
						$coursesWithSameName[] = $existingCourseIndex;
					}
				}


				## gültige Seminargruppe erstellen
				$validSG = str_replace( '-', '/', explode( ' ', $SG )[ 0 ] );

				## Seminargruppen - die Gültige - zu Kurs hinzufügen, Ab Leerzeichen trennen und Trennzeichen ändern
				$course[ 'validsg' ][] = $validSG;

				## ggf. doppelt auftretende Einträge entfernen
				$course[ 'validsg' ] = array_unique( $course[ 'validsg' ] );


				## Hilfsvariable für Kurse mit gleichen Namen und gleichen Seminargruppen
				$courseWithSameSeminarGroup = 0;

				## Alle Kurse mit doppelten Namen durchgehen
				foreach ( $coursesWithSameName as $courseWithSameName )
				{
					## Prüfen, ob Seminargruppen gleich sind
					if ( serialize( sort( $course[ 'sg' ] ) ) == serialize( sort( $courses[ 'byTeacher' ][ $teacher ][ $courseWithSameName ][ 'sg' ] ) ) ){
						## Hilfsvariable hochzählen
						$courseWithSameSeminarGroup++;
						/*
						ENTFERNEN!
							## gültige Seminargruppe erstellen
							$validSG = str_replace( '-', '/', explode( ' ', $SG )[ 0 ] );

							## Seminargruppen - die Gültige - zu Kurs hinzufügen, Ab Leerzeichen trennen und Trennzeichen ändern
							$courses[ 'byTeacher' ][ $teacher ][ $courseWithSameName ][ 'validsg' ][] = $validSG;

							## ggf. doppelt auftretende Einträge entfernen
							$courses[ 'byTeacher' ][ $teacher ][ $courseWithSameName ][ 'validsg' ] = array_unique( $courses[ 'byTeacher' ][ $teacher ][ $courseWithSameName ][ 'validsg' ] );
						*/

						## Seminargruppe hinzufügen
						$courses[ 'byTeacher' ][ $teacher ][ $courseWithSameName ][ 'validsg' ][] = $validSG;

						## ggf. doppelt auftretende Einträge entfernen
						$courses[ 'byTeacher' ][ $teacher ][ $courseWithSameName ][ 'validsg' ] = array_unique( $courses[ 'byTeacher' ][ $teacher ][ $courseWithSameName ][ 'validsg' ] );
					}
				}

				## prüfen, ob Hilfsvariable größer null; Kurs mit gleichen Namen und gleichen Seminargruppen existiert
				if ( $courseWithSameSeminarGroup == 0 ){
					## Kurs existiert noch nicht > anlegen
					$courses[ 'byTeacher' ][ $teacher ][] = $course;
				} 
			}
		}
	}
}


/*
## Folgende Zeilen geben alle Kurse nach Seminargruppe sortiert aus

foreach ( $courses[ 'bySG' ] as $SG => $coursesWeek )
{
	echo '<fieldset><legend>' . $SG . '</legend>';
	foreach ( $coursesWeek as $coursesDay )
	{
		foreach ( $coursesDay as $course )
		{
			echo '"' . $course['name'] . '" -> ' . ( empty( $course['validsg'] ) ? 'Keine Seminargruppen' : implode( $course['validsg'], ', ' ) ) . '<br/>';
		}
		echo '<hr style="border:none; border-bottom:1px solid #ccc"/>';
	}
	echo '</fieldset><br/><br/>';
}

exit;
*/


/*
## Folgende Zeilen geben alle Kurse nach Dozent geordnet aus

foreach ( $courses[ 'byTeacher' ] as $teacher => $courses )
{
	echo '<fieldset><legend>' . $teacher . '</legend>';
	foreach ( $courses as $course )
	{
		#var_dump( $course );
		echo 'Name: "' . $course['name'] . '" <br/>SG: ' . ( empty( $course['sg'] ) ? 'Keine Seminargruppen' : implode( $course['sg'], ', ' ) ) . '<br />ValidSG: ' . ( empty( $course['validsg'] ) ? 'Keine Seminargruppen' : implode( $course['validsg'], ', ' ) ) . '<hr style="border:none; border-bottom:1px solid #ccc"/>';
	}
	echo '</fieldset><br/><br/>';
}

exit;
*/


## alte Seminargruppen löschen
ProgramDB::DeleteAllSeminarGroups();

## Array mit gültigen Seminargruppen
$validSeminarGroups = [];

## Seminargruppen in gültige Form konvertieren
foreach ( $seminarGroupNames as $SG )
{
	## gültige Seminargruppe erstellen
	$validSeminarGroups[] = explode( ' ', $SG )[ 0 ];
}

## doppelte Elemente entfernen
$validSeminarGroups = array_unique( $validSeminarGroups );

## neue Seminargruppen durchgehen
foreach ( $validSeminarGroups as $validSG )
{
	## Nach in der Tabelle hinterlegtem Suchmuster suchen, um in der Datenbank suchen zu können
	$pattern = preg_replace( '/(([\d-_\/]+)(.+))$/i', '', $validSG );

	## Studiengang anhand des Suchmusters ermitteln
	$program = ProgramDB::GetProgramBySeminarGroup( $pattern );

	## Seminargruppe zur Datenbank hinzufügen
	ProgramDB::AddSeminarGroup( $validSG, ( isset( $program[ 'id' ] ) ? $program[ 'id' ] : 0 ) );
}


## Alle Dozenten durchgehen
foreach ( $courses[ 'byTeacher' ] as $teacher => $courses )
{
	## Falls Dozent nocht nicht in der Datenbank ist, Datenbankeintrag erstellen
	if ( !UserUtil::ExistsInDatabase( $teacher ) ){
		UserUtil::Create( $teacher );
	}

	##  Alle Kurse des Dozenten durchgehen
	foreach ( $courses as $course )
	{
		## Zuordnung zu Studiengang/Fachbereich erfolgt anhand der ersten Seminargruppe, die angegeben ist, dies kann also fehlerhaft sein
		## Nach in der Tabelle hinterlegtem Suchmuster suchen, um in der Datenbank suchen zu können
		$pattern = empty( $course[ 'validsg' ] ) ? 'SE' : preg_replace( '/(([\d-_\/]+)(.+))$/i', '', $course[ 'validsg' ][ 0 ] );

		## Studiengang anhand des Suchmusters ermitteln
		$program = ProgramDB::GetProgramBySeminarGroup( $pattern );

		/*var_dump( $course[ 'validsg' ] );
		var_dump( $pattern );
		var_dump( $program );
		echo '<hr/>';*/

		## Seminargruppen zu String wandeln, falls vorhaneden
		$SG = empty( $course[ 'validsg' ] ) ? '' : Base64::Encode( implode( ':', $course[ 'validsg' ] ) );

		## Kurse beantragen
		CourseUtil::SaveOrderToDB( utf8_encode( $course[ 'name' ] ), UserUtil::GetID( $teacher ), 1, true, $program[ 'dep' ], $program[ 'id' ], $SG, null, '' );
	}
}

## Statusdatei abrufen
$lastTimetableAccess = THTimetableUtil::GetLastAccess();

## Auslesestatus setzen
THTimetableUtil::SetLastAccess( 3, $lastTimetableAccess[ 'sg' ], $lastTimetableAccess[ 'sgFound' ], $lastTimetableAccess[ 'sgActual' ] );

## Zurück leiten
Request::Send( 'backend', SEND_VIEW, [ 'site' => 'getprogram' ] );


?>