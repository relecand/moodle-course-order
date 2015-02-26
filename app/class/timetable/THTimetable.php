<?php 

/**
 * THTimetable
 * Klasse zum Auslesen des Stundenplanes 
 * TODO: Zeilen 154, 177 und 194 (@) verbessern.
 * @copyright Robert Leonhardt <leonhardt.rob@gmail.com>
 */
final class THTimetable
{
	/**
	 * URL zum Stundenplan
	 * @var    string
	 * @access private
	 */
	private static $URL = 'https://security.tfh-wildau.de/stundenplan/strpl/index.php';

	/**
	 * standard POST-Parameter
	 * @var    array(string)
	 * @access private
	 */
	private $postData = [
		'formSemester' 			=> '1',
		'formSeminargruppe' 	=> '',
		'formVeranstaltungsart' => '1',
		'formKalenderwoche'		=> 'alle',
		'btnStudenten'			=> ' ',
		'submitted'				=> '1',
		'pl_anzeigen'			=> 'Listenansicht anzeigen'
	];

	/**
	 * benötigte Reqex-Anweisungen
	 * @var    array(string)
	 * @access private
	 */
	private static $Regex = [
		'preg' => [
			'get.all.sg'         	   => '/([\w^_]{32})">([^<]+)/i',
			'get.weekday'        	   => '/<strong>(([a-z]+)|([\d]{2}.[\d]{2}.[\d]{4})){1}/i',
			'get.course.name'    	   => '/title="">(.+)([\/0-9]{3})</Usi',
			'get.course.time'    	   => '/bodytext">([\d :-]+)/i',
			'get.course.teacher' 	   => '/uid=([-_\w]+)/i',
		],
		'explode' => [
			'split.week.in.days'       => '<table',
			'split.days.in.courses'    => '<td colspan="6"',
			'remove.appending.string'  => '</table>',
			'split.course.info'		   => 'bodytext">',
			'remove.appending.content' => '</p>',
			'split.seminar.groups'	   => '<br />',
			'remove.set'			   => ' ',
		]
	];

	/**
	 * alle Seminargruppen
	 * @var    array(string)
	 * @access private
	 */
	private $seminarGroups = [];

	/**
	 * Konstruktormethode zum Auslesen aller verfügbaren Seminargruppen
	 * @access public
	 */
	public function __construct()
	{
		## Alle Seminargruppen laden
		#$this -> seminarGroups = $this -> getAllSeminarGroups();
	}

	/**
	 * Methode zum Auslesen aller Seminargruppen + deren Codes
	 * @return array(string)
	 * @access public
	 */
	public function getAllSeminarGroups()
	{
		## Seminargruppen zurückgeben, falls diese bereits ausgelesen wurden
		if ( !empty( $this -> seminarGroups ) ){
			return $this -> seminarGroups;
		}

		## Quelltext (ohne expliziten Stundenplan) laden und nach Muster durchsuchen
		preg_match_all( self::$Regex[ 'preg' ][ 'get.all.sg' ],  $this -> getHTML(), $seminarGroupsRaw, PREG_SET_ORDER );

		## Exception, wenn keine Seminargruppen gefunden wurden
		if ( empty( $seminarGroupsRaw ) ){
			throw new THTimetableException( 'Seminar groups not found', 1001 );
		}

		## Array mit bereinigten Daten
		$seminarGroups = [];

		## Rohergebnisse entfernen
		foreach ( $seminarGroupsRaw as $seminarGroupRaw )
		{
			## Daten zu berenigtem Array hinzufügen
			$seminarGroups[ $seminarGroupRaw[ 2 ] ] = $seminarGroupRaw[ 1 ];
		}

		## doppelte Elemente entfernen
		$seminarGroups = array_unique( $seminarGroups );

		## Seminargruppen zum Objekt hinzufügen
		$this -> seminarGroups = $seminarGroups;

		## bereinigte Daten zurückliefern
		return $seminarGroups;
	}

	/**
	 * Methode zum Laden von Stundenplänen
	 * @param  string $seminarGroup
	 * @return array(string)
	 * @access public
	 */
	public function getTimetable( $seminarGroup )
	{
		## Quelltext ermitteln (dabei wird Seminargruppe überprüft)
		$timetableHTML = $this -> getHTML( $seminarGroup );

		## Stundenplan in Wochentage aufgliedern (anhand der HTML-Tabellen), anschließend erstes Nullelement entfernen
		$timetableDays = explode( self::$Regex[ 'explode' ][ 'split.week.in.days' ], $timetableHTML );
						 array_shift( $timetableDays );

		## Hauptarray für den gefilterten Stundenplane
		$timetable = [];

		## Alle Wochentage durchgehen
		foreach ( $timetableDays as $day )
		{
			## Wochentag erstellen
			{
				## Nach Wochentag im Quelltext suchen
				preg_match( self::$Regex[ 'preg' ][ 'get.weekday' ], $day, $dayNameSearch );

				## Wochentag für weitere Bearbeitung zwischenspeichern
				$dayName = strtolower( isset( $dayNameSearch[ 1 ] ) ? $dayNameSearch[ 1 ] : 'unknown weekday' );

				## Wochentag im Array anlegen
				$timetable[ $dayName ] = [];
			}

			## Lehrveranstaltungen ausfiltern (anhand der Tabellenspalten), anschließend erstes Nullelement entfernen
			$coursesHTML = explode( self::$Regex[ 'explode' ][ 'split.days.in.courses' ], $day );
					   	   array_shift( $coursesHTML );

			## letztes Element bearbeiten, um HTML-Schwanz abzuschneiden, falls nötig
			@$coursesHTML[ count( $coursesHTML ) - 1 ] = explode( self::$Regex[ 'explode' ][ 'remove.appending.string' ], $coursesHTML[ count( $coursesHTML ) - 1 ] )[ 0 ];

			## Alle Kurse des Tages durchgehen
			foreach ( $coursesHTML as $courseHTML )
			{
				## Kursnamen ermitteln
				{
					## Kursnamen suchen
					preg_match( self::$Regex[ 'preg' ][ 'get.course.name' ], $courseHTML, $courseNameSearch );

					## Kursnamen für weitere Bearbeitung zwischenspeichern
					$courseName = isset( $courseNameSearch[ 1 ] ) ? trim( $courseNameSearch[ 1 ] ) : 'unknown name';

					## Kurs im Array anlegen
					$timetable[ $dayName ][] = [ 'name' => $courseName ];
				}		

				## Array-Key des gerade erstellten Elements ermitteln
				$courseKey = count( $timetable[ $dayName ] ) - 1;

				## Uhrzeit ermitteln (für Vergleich mit eventuell gleichnamigen Kursen)
				{
					## Uhrzeit suchen
					preg_match( self::$Regex[ 'preg' ][ 'get.course.time' ], $courseHTML, $courseTimeSearch );

					## Uhrzeit zum Stundenplan hinzufügen (dabei letztes Leerzeichen entfernen)
					@$timetable[ $dayName ][ $courseKey ][ 'time' ] = substr( $courseTimeSearch[ 1 ], 0, -1 );
				}

				## Dozent ermitteln
				{
					## Dozent suchen
					preg_match_all( self::$Regex[ 'preg' ][ 'get.course.teacher' ], $courseHTML, $courseTeacherSearch );

					## Dozent zum Stundenplan hinzufügen
					$timetable[ $dayName ][ $courseKey ][ 'teacher' ] = isset( $courseTeacherSearch[ 1 ] ) ? $courseTeacherSearch[ 1 ] : 'unknown';
				}  

				## Seminargruppen ermitteln
				{
					## String nochmal aufteilen
					$courseSGSearchString = explode( self::$Regex[ 'explode' ][ 'split.course.info' ], $courseHTML );
					## relevanter l ist der Vorletzte ..
					@$courseSGSearchString = $courseSGSearchString[ count( $courseSGSearchString ) - 2 ];

					## Unwichtige Stringanteile abschneiden
					$courseSGSearchString = explode( self::$Regex[ 'explode' ][ 'remove.appending.content' ], $courseSGSearchString )[ 0 ];

					## Seminargruppen entgültig aufsplitten und letztes Leerelement entfernen
					$courseSG = explode( self::$Regex[ 'explode' ][ 'split.seminar.groups' ], $courseSGSearchString );
					$courseSG = array_filter( $courseSG );
					
					## Seminagruppen hinzufügen und letztes Leerelement entfernen
					foreach( $courseSG as $SG )
					{
						## Komplette Seminargruppe (mit Set etc.)
						$timetable[ $dayName ][ $courseKey ][ 'fullsg' ][] = $SG;

						## bereinigte Seminargruppe (ohne Sets)
						$timetable[ $dayName ][ $courseKey ][ 'sg' ][] = explode( self::$Regex[ 'explode' ][ 'remove.set' ], $SG )[ 0 ];
						#var_dump(explode( self::$Regex[ 'explode' ][ 'remove.set' ], $SG )[ 0 ]); 
					}
				}
			}
		}

		## Stundenplan zurückliefern
		return $timetable;
	}

	/**
	 * Methode zum Prüfen, ob eine Seminargruppe existiert
	 * @param  string $seminarGroup
	 * @return string
	 * @access private
	 */
	private function validateSeminarGroup( $seminarGroup )
	{
		## Seminargruppen auslesen, falls noch nicht geschehen
		if ( empty( $this -> seminarGroups ) ){
			$this -> seminarGroups = $this -> getAllSeminarGroups();
		}

		## Seminargruppe in Array suchen ..
		if ( !isset( $this -> seminarGroups[ htmlentities( $seminarGroup, ENT_IGNORE ) ] ) ){
			throw new THTimetableException( 'Invalid seminar group', 1002 );
		}

		## .. andernfalls zurückgeben
		return $this -> seminarGroups[ htmlentities( $seminarGroup, ENT_IGNORE ) ];
	}

	/**
	 * Methode zum Ermitteln von Stundenplanquelltexten
	 * @param  string $seminarGroup
	 * @return string
	 * @access private
	 */
	private function getHTML( $seminarGroup = false )
	{
		## Seminargruppe validieren
		$seminarGroup = $seminarGroup ? $this -> validateSeminarGroup( $seminarGroup ) : '';

		## Seminargruppe zu POST-Daten hinzufügen
		$this -> postData[ 'formSeminargruppe' ] = $seminarGroup;

		## HTTP-Optionen zusammenstellen
		$httpOpts = [ 'http' => [
			'method'  => "POST",
			'header'  => "Connection: close\r\n".
						 "Content-type: application/x-www-form-urlencoded\r\n".
						 "Content-Length: " . strlen( http_build_query( $this -> postData ) ) . "\r\n",
			'content' => http_build_query( $this -> postData )
		] ];

		## Quelltext auslesen
		$html = file_get_contents( self::$URL, false, stream_context_create( $httpOpts ) );

		## Exception, wenn kein Quelltext angekommen ist
		if ( !$html or $html == '' ){
			throw new THTimetableException( 'Sourcecode not found', 1003 );
		}

		## Quelltext ausgeben und zurückliefern
		return $html;
	}
}

## Exception-Klasse
final class THTimetableException extends Exception {}

?>