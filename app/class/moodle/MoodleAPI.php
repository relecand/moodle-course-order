<?php 
require_once( CONFIG . 'moodleapi.config.local.php' );

/**
 * MoodleAPI.php
 * Klasse zum Zugriff auf Moodle-API
 * @author Robert Leonhardt
 */
class MoodleAPI
{
	/**
	 * Token zur Authentifizierung
	 * @var    string
	 * @access private
	 */
	private $token;

	/**
	 * SOAP-URL
	 * @var    string
	 * @access private
	 */
	private $soapURL;

	/**
	 * SOAP-Objekt
	 * @var    obj
	 * @access private
	 */
	PUBLIC $SoapObject;

	/**
	 * benötigte Soap-XML-Struktur
	 * @var    array(string)
	 * @access private
	 */
	private static $SoapXMLPath = [
		'fault' => '/SOAP-ENV:Envelope/SOAP-ENV:Body/SOAP-ENV:Fault/faultcode'
	];

	/**
	 * API-Funktionen
	 * @var    array(string)
	 * @access private
	 */
	private static $APIFN = [
		'create.course'		  => 'core_course_create_courses',
		'enroll.user'		  => 'enrol_manual_enrol_users',
		'get.courses.by.user' => 'core_enrol_get_users_courses',
		'get.user' 			  => 'core_user_get_users',
		'get.user.by.field'   => 'core_user_get_users_by_field'
	];

	/**
	 * Konstruktormethode zum Aufbau der Verbindung
	 * @access public
	 */
	public function __construct()
	{
		## Token ermitteln (dient gleichzeitig als Funktionstest)
		$this -> token   = self::GetTokenByURL( MOODLE_API_TOKEN_URL );
		#$this -> token = '';

		## SOAP-URL erstellen
		$this -> soapURL = MOODLE_API_SOAP_URL . $this -> token;

		## Soap-Objekt instanziieren
		$this -> SoapObject = new SoapClient( $this -> soapURL );
	}

	/**
	 * Methode zum Ermitteln aller Kurse, in denen der angegebene Nutzer eingeschrieben ist
	 * @param  string $user
	 * @param  bool   $requireTrainer
	 * @return mixed
	 * @access public
	 */
	public function getCoursesByUser( $user, $requireTrainer = true )
	{
		## Parameter-Objekt erstellen
		$Args = new stdClass;
		
		## Benutzer-ID ermitteln
		$Args -> userid = $this -> getUserIDByName( $user );

		## Kurse ermitteln und zurückliefern
		$courses = $this -> SoapObject -> __soapCall( self::$APIFN['get.courses.by.user'], (array)$Args );

		## Ergebnis zurückliefern
		return $courses;
	}

	/**
	 * Methode zum Ermitteln einer Benutzer-ID anhand seines Namens
	 * @param  string $username
	 * @param  bool $required
	 * @return int
	 * @access public
	 */
	public function getUserIDByName( $username, $required = true )
	{
		## Parameter-Objekt erstellen
		$Args = new stdClass;

		## Parameter festlegen
		$Args -> criteria = [ [ 'key' => 'username', 'value' => (string)$username ] ];

		## Benutzerdaten ermitteln
		$userdata = $this -> SoapObject -> __soapCall( self::$APIFN['get.user'], (array)$Args );

		## Prüfen, ob Benutzer-ID gefunden
		if ( isset( $userdata[ 'users' ][ 0 ][ 'id' ] ) ){
			## User-ID gefunden, zurückliefern
			return (int)$userdata[ 'users' ][ 0 ][ 'id' ];
		} else {
			## ID nicht gefunden, prüfen ob schlimm ..
			if ( $required ){
				## User-ID war gefordert > Exception
				throw new MoodleAPIException( 'Required user information not found.', 1011 );
			} else {
				## User-ID ist nicht gefordert > false zurückliefern
				return false;
			}
		}
	}

	/**
	 * Methode zum Anlegen eines Kurses
	 * @param  string $id
	 * @param  string $fullname
	 * @param  string $shortname
	 * @param  int $category
	 * @access public
	 */
	public function createCourse( $id, $fullname, $shortname, $category, $defaultTrainer = null )
	{
		## Parameter-Objekt erstellen
		$Course = new stdClass;

		## Parameter festlegen
		$Course -> idnumber   = $id;
		$Course -> fullname   = $fullname;
		$Course -> shortname  = $shortname;
		$Course -> categoryid = (int)$category;

		## Parameter zu Array
		$courses[0] = (array)$Course;

		## Kurse anlegen und Ergebnis in Variable speichern
		$response = $this -> SoapObject -> __soapCall( self::$APIFN['create.course'], [ $courses ] );

		## Exception, wenn Kurs nicht erstellt wurde
		if ( empty( (array)$response ) ){
			throw new MoodleAPIException( 'Error while creating course.', 1021 );
		}

		## falls Trainer angegeben, diesen verarbeiten
		if ( $defaultTrainer ){
			## Parameter-Objekt erstellen
			$Trainer = new stdClass;

			## Parameter festlegen
			$Trainer -> roleid   = 3; // Trainer = 3
			$Trainer -> userid   = $this -> getUserIDByName( $defaultTrainer );
			$Trainer -> courseid = $response[0]['id'];

			## Objekt zu Array
			$Trainers[0] = (array)$Trainer;

			## Trainer einschreiben
			$this -> SoapObject -> __soapCall( self::$APIFN['enroll.user'], [ $Trainers ] );
			
			var_dump( $Trainer );
		}

		#var_dump( $response );
	}

	/**
	 * Methode zum Ermitteln des Authentifizierungstokens
	 * @param  string $url
	 * @access private
	 */
	private static function GetTokenByURL( $url )
	{
		## Tokenanfrage
		$tokenRequest = @file_get_contents( $url );

		## Fehler, wenn Dateiübermittlung schief lief
		if ( $tokenRequest == null ){
			throw new MoodleAPIException( 'Moodle API access failed. Check URL.', 1001 );
		}

		## Anfrage auswerten
		$tokenObject = json_decode( $tokenRequest );

		## prüfen, ob Objekt erfolgreich erstellt wurde
		if ( !is_a( $tokenObject, 'stdClass' ) ){
			throw new MoodleAPIException( 'Error while receiving token.', 1002 );
		}

		## Fehler ermitteln
		if ( isset( $tokenObject -> error ) ){
			throw new MoodleAPIException( 'Error while receiving token. ("' . $tokenObject -> error . '")', 1003 );
		}

		## Token zurückliefern
		return $tokenObject -> token;
	}
}

## Exception-Klasse
class MoodleAPIException extends Exception {}

?>