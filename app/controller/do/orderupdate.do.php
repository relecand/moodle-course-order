<?php 

/**
 * Seite zum Ändern von Kursanträgen
 * @author Robert Leonhardt
 */

## Daten entgegennehmen
$data = [
	'id' 				=> Request::POST( 'fid', 0 ), // wird in jedem Fall benötigt
	'time' 				=> Request::POST( 'fti', false ),
	'status' 			=> Request::POST( 'fst', false ),
	'name' 				=> Request::POST( 'fnm', false ),
	'user' 				=> Request::POST( 'fus', false ),
	'appendSemester' 	=> Request::POST( 'fas', true ),
	'dep' 				=> Request::POST( 'fdp', false ),
	'program' 			=> Request::POST( 'fpr', false ),
	'sg' 				=> Request::POST( 'fsg', false ),
	'import' 			=> Request::POST( 'fim', false ),
	'additionals' 		=> Request::POST( 'fad', false )
];

## Daten abrufen und dabei prüfen
if ( !$course = CourseDB::GetOrderById( intval( $data[ 'id' ] ) ) ){
	Request::Send( 'applycourses', SEND_VIEW );
}

## Und nun prüfen, ob Benutzer auch Antragseigner (oder Admin) ist
if ( $Session -> getUser() == $course[ 'user' ] or $Session -> isAdmin() ){
	
	## Kursantrag aktualisieren
	CourseUtil::UpdateOrder(
		intval( 	  $course[ 'id' ] ),
		CourseUtil::ValidateCourseName( $data[ 'name' ] ? $data[ 'name' ] 								  : $course[ 'name' ] 		  ),
					  $data[ 'time' ] 					? date( 'Y-m-d H:i:s' ) 						  : $course[ 'time' ],
		intval( 	  $data[ 'user' ] 					? $data[ 'user' ] 								  : $course[ 'user' ] 		  ),
		intval( 	  $data[ 'status' ] 				? $data[ 'status' ] 					  		  : $course[ 'status' ]       ),
		(bool)		  $data[ 'appendSemester' ],
		intval( 	  $data[ 'dep' ] 					? $data[ 'dep' ] 								  : $course[ 'dep' ]          ),
		intval( 	  $data[ 'program' ] 				? $data[ 'program' ] 							  : $course[ 'program' ]      ),
		htmlentities( $data[ 'sg' ] 					? Base64::Encode( implode( ':', $data[ 'sg' ] ) ) : $course[ 'semgrp' ]       ),
		intval(       $data[ 'import' ] 				? $data[ 'import' ] 							  : $course[ 'importsource' ] ),
		htmlentities( $data[ 'additionals' ] 			? $data[ 'additionals' ] 						  : $course[ 'additionals' ]  )
	);
	
}

## Zurück leiten
Request::Send( 'applycourses', SEND_VIEW );

?>