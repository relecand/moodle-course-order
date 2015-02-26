<?php 

/**
 * CourseUtil.php
 * Klasse mit Hilfsmethoden zur Kursverarbeitung
 * @author Robert Leonhardt
 */
abstract class CourseUtil
{
	/**
	 * Array mit benötigten RegEx-Anweisungen
	 * @var array(string)
	 */
	private static $RegEx = [

	];

	/**
	 * Methode zum Validieren von Kursbezeichnungen
	 * @param  string $coursename
	 * @return string
	 * @access public
	 */
	public static function ValidateCourseName( $coursename )
	{
		## vorerst nur XSS-Filter ..
		return htmlentities( $coursename );
	}

	/**
	 * Methode zum Generieren des Kurznamen des Kurses
	 * @param  string $coursename
	 * @return string
	 * @access public 
	 */
	public static function GenerateShortCourseName( $coursename )
	{
		## einzelne Teile ermitteln
		$nameAsArray = explode( ' ', $coursename );

		## neue Variable definieren
		$name = '';

		## Name neu, gekürzt zusammensetzen
		foreach ( $nameAsArray as $word )
		{
			## erste zwei Buchstaben beürcksichtigen ..
			$name .= substr( $word, 0, 2 );
		}

		## fertigen Namen zurückliefern
		return $name;
	}

	/**
	 * Methode zum Eintragen des Kursantrages in die Datenbank (front)
	 * @param  string $name
	 * @param  int    $user
	 * @param  int    $status
	 * @param  bool   $appendSemester
	 * @param  int    $program
	 * @param  int    $dep
	 * @param  string $semgroups
	 * @param  string $import
	 * @param  string $additionalInfo
	 * @access public
	 */
	public static function SaveOrderToDB( $name, $user, $status = null, $appendSemester = null, $dep = null, $program = null, $semgroups = null, $import = null, $additionalInfo = null )
	{
		## Daten zusammenstellen
		$data = [
			':name' 		  => $name,
			':user'			  => $user,
			':status'		  => $status,
			':appendSemester' => (int)$appendSemester,
			':dep' 			  => (int)$dep,
			':program' 		  => (int)$program,
			':semgrp' 		  => $semgroups,
			':import' 		  => $import,
			':additionals' 	  => $additionalInfo 
		];

		## Kursantrag zur Datenbank hinzufügen
		CourseDB::AddOrder( $data );
	}

	/**
	 * Methode zum Ändern von Kursanträgen
	 * @param  ínt    $id
	 * @param  string $name
	 * @param  string $time
	 * @param  int    $user
	 * @param  int    $status
	 * @param  bool   $appendSemester
	 * @param  int    $program
	 * @param  int    $dep
	 * @param  string $semgroups
	 * @param  string $import
	 * @param  string $additionalInfo
	 * @access public
	 */
	public static function UpdateOrder( $id, $name, $time, $user, $status = null, $appendSemester = null, $dep = null, $program = null, $semgroups = null, $import = null, $additionalInfo = null )
	{
		## Daten zusammenstellen
		$data = [
			':id'   		  => $id,
			':name' 		  => $name,
			':time' 		  => $time,
			':user'			  => $user,
			':status'		  => $status,
			':appendSemester' => (int)$appendSemester,
			':dep' 			  => (int)$dep,
			':program' 		  => (int)$program,
			':semgrp' 		  => $semgroups,
			':import' 		  => $import,
			':additionals' 	  => $additionalInfo 
		];

		## Kursantrag zur Datenbank hinzufügen
		CourseDB::UpdateOrder( $data );
	}
}

/**
 * Exception-Klasse
 */
class CourseUtilException extends Exception {}

?>