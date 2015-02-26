<?php 
require_once( CONFIG . 'THTimetable.config.php' );

/**
 * THTimetableUtil.php
 * Klasse mit Hilfmethoden zur Verarbeitung des Stundenplanes
 */
abstract class THTimetableUtil
{
	/**
	 * Methode zum Auslesen des Zeitpunktes des letzten Ladens
	 * @return int
	 * @access public
	 */
	public static function GetLastAccess()
	{
		## Pfad zur entsprechenden Datei
		$lastAccessFile = THTT_DATA . 'last.access';

		## 0 zurückgeben, wenn Datei nicht existent
		if ( !file_exists( $lastAccessFile ) ){
			return 0;
		}

		## Dateiinhalt zurückgeben
		return unserialize( file_get_contents( $lastAccessFile ) );
	}

	/**
	 * Methode zum Setzen einer Datei mit der Angabe des Zeitpunktes des letzten Ladens
	 * @param  int  $status
	 * @param  int  $teacher
	 * @param  int  $courses
	 * @access public
	 */
	public static function SetLastAccess( $status = 0, array $sg = array(), $sgFound = 0, $sgActual = 0, $sgDownloaded = 0, $teacher = 0, $courses = 0 )
	{
		## Datei schreiben
		$lastAccessFile = THTT_DATA . 'last.access';

		## Datei schreiben
		@file_put_contents( $lastAccessFile, serialize( [ 
			'timestamp'    => TIME_NOW, 
			'semester'     => ACTUAL_SEMESTER,
			'status'  	   => (int)$status,
			'sg'		   => $sg,
			'sgFound'	   => (int)$sgFound,
			'sgActual' 	   => (int)$sgActual,
			'sgDownloaded' => (int)$sgDownloaded,
			'teacher'      => (int)$teacher,
			'courses'      => (int)$courses
		] ) );

		## Exceptiuon, wenn Datei nicht lesbar (also nicht beschrieben wurde)
		if ( self::GetLastAccess() === 0 ){
			throw new THTimetableUtilException( 'Unable to write access file', 1001 );
		}
	}
}

## Exception-Klasse
class THTimetableUtilException extends Exception {}

?>