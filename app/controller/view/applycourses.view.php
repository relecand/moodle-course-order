<?php 

/**
 * applycourses.view.php
 * Seite mit ausgelesenen Kursen, die direkt vom Dozenten beantragt werden können
 * @author Robert Leonhardt
 */

## Seitentitel setzen
$HTML -> setTitle( 'Meine Kurse für dieses Semester' );

## Dozent
$teacher = 'ewolf';

## Kurse des Dozenten ermitteln
$orders = CourseDB::GetOrdersByTeacher( UserUtil::GetID( $teacher ) );

#var_dump( $orders ); exit;

## Auswahl für bisherige Kurse des Nutzers
{
	## Benutzerdaten ermitteln
	$user 			 = UserUtil::GetDataByID( $Session -> getUser() );

	## Alle Kurse des Benutzers ermitteln
	// $userCourses  = $MoodleAPI -> getCoursesByUser( $user['name'] );
	$userCourses     = $MoodleAPI -> getCoursesByUser( 'klako' );

	## HTML-Liste erstellen
	$userCoursesList = '<select name="fim"><option value="false" selected>Keine Kursübernahme</option>';
	foreach ( $userCourses as $course )
	{
		$userCoursesList .= '<option value="' . $course['id'] . '">' . $course['fullname'] . '</option>';
	}
	$userCoursesList .= '</select>';
}

## Kursvorschläge die aus dem Stundenplan stammen
{
	## Liste mit Kursvorschlägen erstellen
	$sl = '';

	## Alle Kursanträge des Dozenten durchgehen
	foreach ( $orders as $order )
	{
		## prüfen, ob status = 1 ist und Kurs somit ein vorgeschlagener ist
		if ( intval( $order[ 'status' ] ) == 1 ){
			## Kurs zur Liste hinzufügen
			$sl .= HTMLUtil::Snippet( 'content.applycourses.slitem.html', [
				'id'      => $order[ 'id' ],
				'name'    => $order[ 'name' ] . ' - ' . ACTUAL_SEMESTER,
				'dep'     => ProgramDB::GetDepartmentById( $order[ 'dep' ] )[ 'shortname' ],
				'program' => ProgramDB::GetProgramById( $order[ 'program' ] )[ 'name' ],
				'import'  => $userCoursesList,
				'sg'      => $order[ 'semgrp' ] == '' ? 'Keine Seminargruppen vorhanden.' : str_replace( ':', ', ', Base64::Decode( $order[ 'semgrp' ] ) )
			] );
		}
	}
}

## Array mit Inhalten, die ins Template eingesetzt werden
$content = [
	'sl' => $sl != '' ? $sl : 'Keine Kursvorschläge vorhanden ..'
];

## Inhalt einfügen
$HTML -> replace( 'body.content', HTMLUtil::Snippet( 'content.applycourses.html', $content ) );

## Javascript laden
$HTML -> includeHeadFile( 'theme/js/jq.js' );
$HTML -> includeHeadFile( 'theme/js/site.applycourses.js' );

?>