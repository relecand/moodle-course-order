<?php 

/**
 * Klasse zur Datenbankverarbeitung von Kursen, Studiengängen und Fachbereichen
 * @author Robert Leonhardt
 */
abstract class ProgramDB
{
	/**
	 * Array mit SQL-Anweisungen
	 * @var    array(string)
	 * @access private
	 */
	private static $SQL = [
		'course.get.by.program' => 'select * from course where program = :program order by sem;',
		'data.get.by.program'   => 'select d.id as did, d.name as dname, d.shortname as dshortname, p.id as pid, p.name as pname, p.shortname as pshortname from dep as d, program as p where p.dep = d.id and p.id = :program',
		'department.get.all'    => 'select * from dep;',
		'department.get.by.id'  => 'select * from dep where id = :dep;',
		'program.get.all'	    => 'select * from program order by type, name;',
		'program.get.by.sg'		=> 'select * from program where sgpattern = :sgpattern;',
		'program.get.by.id'     => 'select * from program where id = :program;',
		'sg.insert'				=> 'insert into sg ( name, program ) values ( :name, :program )',
		'sg.delete.all'			=> 'truncate table sg;'
	];

	/**
	 * Methode zum Laden von Kursen anhand des Studienganges
	 * @param  int $program
	 * @return array(mixed)
	 * @access public
	 */
	public static function GetCoursesByProgram( $program )
	{
		## Statementhandler
	  	$STH = Database::GetPrepared( self::$SQL['course.get.by.program'] );

    	## SQL ausführen
	    $STH -> execute( array( ':program' => $program ) );

	    ## Ergebnis zurückliefern
	    return $STH -> fetchAll();
	}

	/**
	 * Methode zum Ermitteln aller Fachbereiche
	 * @return array(string)
	 * @access public
	 */
	public static function GetDepartments()
	{
		## Statementhandler
	    $STH = Database::GetPrepared( self::$SQL['department.get.all'] );

	    ## SQL ausführen
	    $STH -> execute();

	    ## Ergebnis zurückliefern
	    return $STH -> fetchAll();
	}

	/**
	 * Methode zum Ermitteln von Kursdaten (Struktur) anhand seiner ID
	 * @param  int $program
	 * @return array(mixed)
	 * @access public
	 */
	public static function GetDataByProgram( $program )
	{
		## Statementhandler
	  	$STH = Database::GetPrepared( self::$SQL['data.get.by.program'] );

    	## SQL ausführen
	    $STH -> execute( array( ':program' => $program ) );

	    ## Ergebnis zurückliefern
	    return $STH -> fetchAll();
	}

	/**
	 * Methode zum Ermitteln der Fachbereichsdaten anhand der ID
	 * @param  int $dep
	 * @return array(mixed)
	 * @access public
	 */
	public static function GetDepartmentById( $dep )
	{
		## Statementhandler
	  	$STH = Database::GetPrepared( self::$SQL['department.get.by.id'] );

    	## SQL ausführen
	    $STH -> execute( array( ':dep' => $dep ) );

	    ## Ergebnis zurückliefern
	    return $STH -> fetch();
	}

	/**
	 * Methode zum Ermitteln der Studiengangsdaten anhand der ID
	 * @param  int $program
	 * @return array(mixed)
	 * @access public
	 */
	public static function GetProgramById( $program )
	{
		## Statementhandler
	  	$STH = Database::GetPrepared( self::$SQL['program.get.by.id'] );

    	## SQL ausführen
	    $STH -> execute( array( ':program' => $program ) );

	    ## Ergebnis zurückliefern
	    return $STH -> fetch();
	}

	/**
	 * Methode zum Ermitteln aller Studiengänge
	 * @return array(string)
	 * @access public
	 */
	public static function GetPrograms()
	{
		## Statementhandler
	    $STH = Database::GetPrepared( self::$SQL['program.get.all'] );

	    ## SQL ausführen
	    $STH -> execute();

	    ## Ergebnis zurückliefern
	    return $STH -> fetchAll();
	}

	/**
	 * Methode zum Ermitteln von Studiengangsdaten anhand der Seminargruppennamen
	 * @param  string $sgpattern
	 * @return array(mixed)
	 * @access public
	 */
	public static function GetProgramBySeminarGroup( $sgpattern )
	{
		## Statementhandler
	  	$STH = Database::GetPrepared( self::$SQL['program.get.by.sg'] );

    	## SQL ausführen
	    $STH -> execute( array( ':sgpattern' => $sgpattern ) );

	    ## Ergebnis zurückliefern
	    return $STH -> fetch();
	}

	/**
	 * Methode zum hinzufügen von Seminargruppen
	 * @param  string $name
	 * @param  int $program
	 * @access public
	 */
	public static function AddSeminarGroup( $name, $program )
	{
		## Statementhandler
	    $STH = Database::GetPrepared( self::$SQL['sg.insert'] );

	    ## SQL ausführen und Platzhalter ersetzen
	    $STH -> execute( [
	      ':name'    => $name,
	      ':program' => (int)$program
	    ] );
	}

	/**
	 * Methode zum Leeren der Seminargruppentabellen
	 * @return mixed
	 * @access public
	 */
	public static function DeleteAllSeminarGroups()
	{
		## Statementhandler
	    $STH = Database::GetPrepared( self::$SQL['sg.delete.all'] );

	    ## SQL ausführen
	    $STH -> execute();
	}
}

?>