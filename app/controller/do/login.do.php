<?php 

/**
 * login.do.php
 * Datei zum Anmelden
 * @author Robert Leonhardt
 */

## Wenn der Benutzer bereits eingeloggt ist, soll er gefälligst zur Startseite zurück
if ( $Session -> getUser() != null ){
  Request::Send( 'main' );
}


## Daten ermitteln
$data = array(
  'user'     => Request::POST( 'n', false ),
  'password' => Request::POST( 'p', false ),
  'submit'   => Request::POST( 's', false )
);


## Zur Loginseite zurück, wenn Angaben fehlen
if ( !$data['user'] or !$data['password'] or !$data['submit'] ){
  Request::Send( 'login', Request::SEND_VIEW, array( 'il' => 10 ) );
}


## Verbindung zum TH-LDAP-Server herstellen
$THLDAP = new THLDAP;

/*
## Zur Loginseite zurück, wenn LDAP-Login fehlschlägt
if ( !$THLDAP -> validateLogin( $data['user'], $data['password'] ) ){
  Request::Send( 'login', Request::SEND_VIEW, array( 'il' => 20 ) );
}
*/


## Prüfen, ob der der Benutzer in der eigenen (!= LDAP-) Datenbank befindet ...
if ( !UserUtil::ExistsInDatabase( $data['user'] ) ){

  ## Benutzer wird zur Loginseite geleitet, falls er kein Mitarbeiter ist
  if ( !$THLDAP -> isCoworker( $data['user'] ) ){
    Request::Send( 'login', Request::SEND_VIEW, array( 'il' => 30 ) );
  }

  ## Benutzer ist Mitarbeiter, hat jedoch noch keinen Datenbankeintrag - das wird jetzt erledigt
  UserUtil::Create( $data['user'] );
}


## Session anmelden; Anmeldung abgeschlossen
$Session -> changeUser( UserUtil::GetID( $data['user'] ) );

?>