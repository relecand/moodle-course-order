<?php 

/**
 * Seite Verbergen von Kursanträgen (Dozentenseitig)
 * @author Robert Leonhardt
 */

## Kursantrags-ID ermitteln
$course = Request::DATA( 'cid', false );

## zurück, wenn keine ID übermittelt
if ( !$course ){
	Request::Send( 'applycourses', SEND_VIEW );
}

## Kursdaten ermitteln
$course = CourseDB::GetOrderById( intval( $course ) );

## Auch zurückleiten, wenn kein Kurs gefunden wurde ..
if ( !$course ){
	Request::Send( 'applycourses', SEND_VIEW );
}

## Und nun prüfen, ob Benutzer auch Antragseigner (oder Admin) ist, damit man nicht fremde Kursanträge verschwinden lassen kann ..
if ( $Session -> getUser() == $course[ 'user' ] or $Session -> isAdmin() ){
	## Kursantrag deaktivieren
	CourseDB::HideOrder( $course[ 'id' ] );
}

## Zurück leiten
Request::Send( 'applycourses', SEND_VIEW );

?>