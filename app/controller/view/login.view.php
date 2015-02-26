<?php 

/**
 * login.view.php
 * Seite zum Login
 */

## Prüfen, ob Benutzer eingeloggt

## Seitentitel setzen
$HTML -> setTitle( 'Anmelden' );

## Login-Fomular einfügen
$HTML -> replace( 'body.content', HTMLUtil::Snippet( 'content.login-form.html' ) );

?>