<?php 

/**
 * backend.resetdb.view.php
 * Unterseite mit Benutzerverwaltung
 * @author Robert Leonhardt
 */

## Seitentitel setzen
$HTML -> setTitle( 'Administration - Bugs und Changelogs' );

## Inhalt zur Ausgabe hinzufügen
$content[ 'site' ] = HTMLUtil::Snippet( 'content.backend.bugs.html', [] );

?>