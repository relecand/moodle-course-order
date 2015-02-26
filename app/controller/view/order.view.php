<?php 

/**
 * order.view.php
 * Kursantragsseite
 * @author Robert Leonhardt
 */

## Auswahl für Studiengang
{
	## Fachbereiche ermitteln
	$dep 	  = ProgramDB::GetDepartments();

	## Studiengänge ermitteln
	$programs = ProgramDB::GetPrograms();

	## Array mit geordneter Liste
	$order    = $dep;

	## Studiengänge den Fachbereichen zuordnen
	foreach ( $programs as $program )
	{
		$order[ $program['dep'] - 1 ]['programs'][] = $program; //'(' . $program['id'] . ') ' . ( $program['type'] == 1 ? 'Bachelor' : 'Master' ) . ' - ' . $program['name'];
	}

	## HTML-Liste erstellen
	{
		## String erstellen 
		$programList = '<select name="cs"><option value="false" selected>Bitte auswählen</option>';

		## Alle Fachbereiche durchgehen
		foreach ( $order as $dep )
		{
			## Unterelemente erstellen
			$programList .= '<optgroup label="' . $dep['name'] . '">';

			## Wenn Unterkategorien existieren (eig. immer), diese hinzufügen
			if ( isset( $dep['programs'] ) ){
				foreach ( $dep['programs'] as $program )
				{
					## Kursart ermitteln
					switch ( $program['type'] )
					{
						case 1:  $type = 'Bachelor'; break;
						case 2:  $type = 'Master';   break;
						default: $type = 'Sonstige'; break;
					}
					$programList .= '<option value="' . $program['id'] . '" id="' . $program['type'] . '">' . $type . ' - ' . $program['name'] . '</option>';
				}
			}
			$programList .= '</optgroup>';
		}

		## Stringschließen
		$programList .= '</select>';
	}
}

## Auswahl für bisherige Kurse des Nutzers
{
	## Benutzerdaten ermitteln
	$user 			 = UserUtil::GetDataByID( $Session -> getUser() );

	## Alle Kurse des Benutzers ermitteln
	// $userCourses  = $MoodleAPI -> getCoursesByUser( $user['name'] );
	$userCourses     = $MoodleAPI -> getCoursesByUser( 'klako' );

	## HTML-Liste erstellen
	$userCoursesList = '<select name="cfoc"><option value="false" selected>Bitte auswählen</option>';
	foreach ( $userCourses as $course )
	{
		$userCoursesList .= '<option value="' . $course['id'] . '">' . $course['fullname'] . '</option>';
	}
	$userCoursesList .= '</select>';
}

## Seitentitel setzen
$HTML -> setTitle( 'Neuen Kurs beantragen' );

## Kurs-Fomular einfügen
$HTML -> replace( 'body.content', HTMLUtil::Snippet( 'content.order-form.html', [
	'select.program' => $programList,
	'select.course'  => $userCoursesList,
	'semester'		 => ACTUAL_SEMESTER
] ) );

## Javascript laden
$HTML -> includeHeadFile( 'theme/js/jq.js' );
$HTML -> includeHeadFile( 'theme/js/site.order-form.js' );

?>