<?php 

/**
 * Seite zum Zurücksetzen der Datenbanken
 * @author Robert Leonhardt
 */

## Zur Startseite (Bestellseite), wenn Benutzer kein Admin ist
if ( !$Session -> isAdmin() ){
  Request::Send( 'main' );
}

## alle Sessions löschen
SessionDB::ClearAllSessions();

## alte Seminargruppen löschen
ProgramDB::DeleteAllSeminarGroups();

## alle Anträge löschen
CourseDB::DeleteAllOrders();

## alle Nichtadmins löschen
#UserDB::DeleteAllNonadmins();

## Stundenplanauslesestatus einen Schritt zurücksetzen
## Statusdatei abrufen
$lastTimetableAccess = THTimetableUtil::GetLastAccess();

## Auslesestatus setzen
THTimetableUtil::SetLastAccess( 2, $lastTimetableAccess[ 'sg' ], $lastTimetableAccess[ 'sgFound' ], $lastTimetableAccess[ 'sgActual' ] );

?>