<?php 

/**
 * moodleapi.config.php
 * @author Robert Leonhardt
 */

## Moodle Server
define( 'MOODLE_API_SERVER',   	 'https://elearning2.th-wildau.de/' );
#define( 'MOODLE_API_SERVER',     'https://193.175.213.147/' );

## Moodle Werbservicename
define( 'MOODLE_API_WEBSERVICE', 'cc-2014' );

## API-Benutzer
define( 'MOODLE_API_USER', 	     'kursbestellsystem' );
## API-Benutzer Passwort
define( 'MOODLE_API_PASSWORD',   'geheim' );

## Token-URL
define( 'MOODLE_API_TOKEN_URL',	 MOODLE_API_SERVER . 'login/token.php?username=' . MOODLE_API_USER . '&password=' . MOODLE_API_PASSWORD . '&service=' . MOODLE_API_WEBSERVICE );
## Token (behilfsmäßig)
define( 'MOODLE_API_TOKEN',		 '608d8862130dc17a480b9e1eb7a5d529' );

## SOAP-URL
define( 'MOODLE_API_SOAP_URL',   MOODLE_API_SERVER . 'webservice/soap/server.php?wsdl=1&token=' );

?>