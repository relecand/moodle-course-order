<?php 

/**
 * logout.do.php
 * Seite zum Abmelden von benutzern
 * @author Robert Leonhardt
 */

## Nur ausloggen, wenn Benutzer angemeldet
if ( $Session -> getUser() != null ){
  ## Session deaktivieren
  $Session -> destroy();
}

## Zur Startseite (und von dort zum Login)
Request::Send( 'main' );

?>