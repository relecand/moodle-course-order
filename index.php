<?php 

/**
 * cc.elearning.dev.com (elearning-Seite zum Erstellen/Bearbeiten von Kursanträgen)
 * Indexdokument zum Entgegennehmen aller Requests
 * @author  Robert Leonhardt
 * @date    2014/08/12
 * @version 0.2
 */

## Session starten
$Session   = new Session;

## Moodle API starten
$MoodleAPI = new MoodleAPI;

## Für die Verarbeitung nutzbare Requestkonstanten definieren
define( 'REQUEST_DO',   Request::DATA( 'do',   false  ) );
define( 'REQUEST_VIEW', Request::DATA( 'view', 'main' ) );

## Controller laden, ?do=bla&view=blubb übergeben, Validierung übernimmt Klasse
require_once( new Controller( REQUEST_DO, REQUEST_VIEW ) );

?>