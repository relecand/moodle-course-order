<?php 

/**
 * requestcourse.do.php
 * Datei zum Empfang der Kursanträge
 * @author Robert Leonhardt
 */

## Daten ermitteln
$data = array(
  	'coursename'     => Request::POST( 'fcn', 	 false ),
  	'program'        => Request::POST( 'fcs', 	 false ),
  	'seminargroups'  => Request::POST( 'fsg', 	 false ),
  	'additionals'    => Request::POST( 'fad', 	 false ),
  	'importsrc'      => Request::POST( 'fic',	 false ),
  	'ignoresemester' => Request::POST( 'fis',	 false ),
  	'submit'		 => Request::POST( 'submit', false )
);

## Zurück zum Formular, wenn keine (der geforderten) Angaben vorhanden sind
if ( !$data['coursename'] or !$data['submit'] ){
	Request::Send( 'main', Request::SEND_VIEW, array( 'mi' ) );
}

## Array mit detailiertem Kursantrag zusammenstellen
$request = [];

## Namen erstellen ..
$request[ 'coursename' ][ 'default' ] 	= CourseUtil::ValidateCourseName( $data[ 'coursename' ] );
$request[ 'coursename' ][ 'full'    ] 	= $request[ 'coursename' ][ 'default' ] . ( $data['ignoresemester'] ? '' : ' - ' . ACTUAL_SEMESTER  );
$request[ 'coursename' ][ 'short'   ] 	= CourseUtil::GenerateShortCourseName( $request[ 'coursename' ][ 'default' ] ) . ACTUAL_SEMESTER;

## Kurs-ID generieren ..
$request[ 'courseid' ] 				  	= Base64::Encode( TIME_NOW . $request[ 'coursename' ][ 'short' ], 16 );

## Wenn Studiengang angegeben ist, dessen Strukturinformationen laden
$info = $data[ 'program' ] ? $info = ProgramDB::GetDataByProgram( (int)$data['program'] ) : false;

## Struktur ermitteln ..
$request[ 'department' ][ 'id' 		  ] = $data ? $info[0][ 'did'        ] : 4; // TODO: hier Info für zuordnunge einfügen ..
$request[ 'department' ][ 'name' 	  ] = $data ? $info[0][ 'dname'      ] : 'fachbereichsübergreifender Kurs';
$request[ 'department' ][ 'shortname' ] = $data ? $info[0][ 'dshortname' ] : 'fbük';
$request[ 'program'    ][ 'id' 		  ] = $data ? $info[0][ 'pid'        ] : 0;
$request[ 'program'    ][ 'name' 	  ] = $data ? $info[0][ 'pname'      ] : '';
$request[ 'program'    ][ 'shortname' ] = $data ? $info[0][ 'pshortname' ] : '';

## Seminargruppen feststellen
$request[ 'seminargroups.name' ]		= explode( ';', $data[ 'seminargroups' ], -1 );
$request[ 'seminargroups.string' ]		= Base64::Encode( implode( ':', $request[ 'seminargroups.name' ] ) );

## Importkurs feststellen
$request[ 'import' ] 					= $data[ 'importsrc' ] ? htmlentities( $data[ 'importsrc' ] ) : null;

## Hinweise etc.
$request[ 'additionals' ]				= $data[ 'additionals' ];

## Kursantrag in Datenbank speichern
CourseUtil::SaveOrderToDB( $request[ 'coursename' ][ 'default' ], (int)$Session -> getUser(), 2, true, $request[ 'program' ][ 'id' ],
						   $request[ 'department' ][ 'id' ], $request[ 'seminargroups.string' ], $request[ 'import' ], $request[ 'additionals' ] );

Request::Send( 'order', SEND_VIEW, [], '#!cns' );

/*var_dump( $request );

exit;*/

?>