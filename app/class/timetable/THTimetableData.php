<?php

/**
 * THTimetableData
 * Klasse zum Filtern, sortieren und Verarbeiten von Stundenplänen
 * @copyright Robert Leonhardt <leonhardt.rob@gmail.com>
 */
final class THTimetableData
{
	/**
	 * Stundenplan-Objekt zur Manipulation
	 * @var    THTimetable
	 * @access private
	 */
	private $TimetableObject;

	/**
	 * Konstruktor zum Übergeben des Stundenplanobjektes
	 * @param  THTimetable $TimetableObject
	 * @access public
	 */
	public function __construct( THTimetable $timetableObject )
	{
		## Objekt übernehmen
		$this -> TimetableObject = $timetableObject;
	}

	/**
	 * Methode zum Ermitteln aller Kurse einer Seminargruppe sortiert nach Kursnamen
	 * @param  string $seminarGroup
	 * @param  bool   $merge
	 * @return array(mixed)
	 * @access public
	 */
	public function getCoursesBySeminarGroup( $seminarGroup )
	{
		## Stundenplan ermitteln
		$timetable = $this -> TimetableObject -> getTimetable( $seminarGroup );

		## Ergebnis-Array erstellen
		$courses   = [];

		## Stundenplan durchgehen
		foreach ( $timetable as $day )
		{
			## Alle Kurse durchgehen
			foreach ( $day as $course )
			{
				## Prüfen, ob Kurs schon gelistet ist ..
				if ( !isset( $courses[ $course[ 'name' ] ] ) ){
					## .. andernfalls zu Array hinzufügen
					$courseToApply = [
						'name' 	   => $course[ 'name' ],
						'teacher'  => $course[ 'teacher' ],
						'time'	   => [ $course[ 'time' ] ],
						'sg'	   => $course[ 'sg' ],
						'instance' => [ $course ]
					];
				} else {
					## Daten des bestehenden Kurses ermitteln
					$existingCourse = $courses[ $course[ 'name' ] ];

					## Kursangaben zusammenführen
					$courseToApply = [
						'name'     => $course[ 'name' ],
						'teacher'  => array_merge( $existingCourse[ 'teacher' ],    $course[ 'teacher' ] ),
						'time'     => array_merge( $existingCourse[ 'time' ],     [ $course[ 'time' ] ] ),
						'sg'       => array_merge( $existingCourse[ 'sg' ],         $course[ 'sg' ] ),
						'instance' => array_merge( $existingCourse[ 'instance' ], [ $course ] )
					];
				}

				## Daten noch anpassen (ungewollte, doppelte Angaben entfernen)
				$courseToApply[ 'teacher' ]   = array_unique( $courseToApply[ 'teacher' ] );
				$courseToApply[ 'sg' ]        = array_unique( $courseToApply[ 'sg' ] );

				## Kursinstanz hinzufügen
				$courses[ $course[ 'name' ] ] = $courseToApply;
			}
		}

		## Ergebnis zurückliefern
		return $courses;
	}

	/**
	 * Methode zum Ermitteln aller Kurse einer Seminargruppe als String
	 * @param  string $seminarGroup
	 * @param  bool   $merge
	 * @return string
	 * @access public
	 */
	public function getCoursesBySeminarGroupAsString( $seminarGroup )
	{
		## Kurse auslesen und zu String umwandeln, diesen dann zurückgeben
		return serialize( $this -> getCoursesBySeminarGroup( $seminarGroup ) );
	}

	/**
	 * Methode zum Ruückliefern aller Seminargruppen
	 * @return array(mixed)
	 * @access public
	 */
	public function getAllSeminarGroups()
	{
		## Seminargruppen auslesen und zurückliefern
		return $this -> TimetableObject -> getAllSeminarGroups();
	}
}

## Exception-Klasse
final class THTimetableDataException extends Exception {}

?>