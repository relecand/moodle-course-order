<?php 
require_once( CONFIG . 'moodleapi.config.local.php' );

/**
 * webservicetest.view.php
 * Webservice Testseite
 * @author Robert Leonhardt
 */

/**
 * Fahrplan, moodleseitig:
 * 1.) Webservices aktivieren
 * 2.) benötigte Protokolle (REST) freigeben
 * 3.) Webservice-Benutzer (MOODLE_API_USER:MOODLE_API_PASSWORD) erstellen
 * 4.) Webservice-Rolle erstellen und sämtliche Webservice-Rechte gewähren
 * 5.) Webservice-Benutzer der Webservice-Rolle hinzufügen
 * 4.) Webservice erstellen (MOODLE_API_WEBSERVICE)
 * 5.) Webservice-Benutzer als befähigten Benutzer hinzufügen
 * 6.) benötigte Methoden für Webservice freischalten
 * 7.) Token erstellen
*/


#$MoodleAPI -> createCourse( 'gbcc60', 'Automatisiert erstellter Kurs #6 (mit eingeschriebenem Trainer)', 'aek6', 2, 'klako' );

#$MoodleAPI -> getCoursesByUser( 'klako' );

#var_dump( $MoodleAPI );

?>