<?php 

/**
 * Ajaxdokument zum Laden der Kurse für das Kursantragsformular
 * @author Robert Leonhardt
 */

## Parameter empfangen
$program = Request::DATA( 'program', false );

## Nur etwas zurückgeben, wenn Parameter übergeben (anderfalls spielt jemand mit dem HTML-Code)
if ( $program ){
	## Kurse anhand des Semesters ermitteln
	$courses = ProgramDB::GetCoursesByProgram( (int)$program );

	## nur weitermachen, wenn Array NICHT(!) leer (und Abfrage daher erfolgreich)
	if ( !empty( $courses ) )
	{
		## Liste zusammenstellen
		$elements = [];

		## Alle gefundenen Kurse durchgehen und nach Semester sortieren
		foreach ( $courses as $course )
		{
			$elements[ $course['sem'] ][] = $course;
		}

		## HTML-Liste zusammenstellen
		$list = '<option value="false" selected>Bitte auswählen</option>';
		foreach ( $elements as $key => $element )
		{
			$list .= '<optgroup label="' . $key . '. Semester">';
			foreach ( $element as $course )
			{
				$list .= '<option value="' . $course['id'] . '">' . $course['name'] . '</option>';
			}
			$list .= '</optgroup>';
		}

		echo $list;
	} else {
		## Fehlermeldung
		echo '<option value="false" selected>Für diesen Studiengang sind leider keine Kurse verfügbar</option>';
	}
}

## Exitbefehl vermeidet Fehler
exit;

?>