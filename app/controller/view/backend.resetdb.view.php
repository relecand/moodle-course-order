<?php 

/**
 * backend.resetdb.view.php
 * Unterseite mit Benutzerverwaltung
 * @author Robert Leonhardt
 */

## Seitentitel setzen
$HTML -> setTitle( 'Administration - Datenbanken zurücksetzen' );

## Inhalt zur Ausgabe hinzufügen
$content[ 'site' ] = HTMLUtil::Snippet( 'content.backend.resetdb.html', [] );

?>