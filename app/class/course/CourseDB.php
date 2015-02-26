<?php 

/**
 * CourseDB.php
 * Klasse mit Datenbankverarbeitung für Kursanträge
 * @author Robert Leonhardt
 */
abstract class CourseDB
{
	/**
	 * Array mit benötigten SQL-Anweisungen
	 * @var array(string)
	 * @access private
	 */
	private static $SQL = [
		'add.order' 		   => 'insert into `order`  ( name, status, user, appendSemester,  dep,  program,  semgrp, importsource,  additionals ) 
							   	   			values ( :name, :status, :user, :appendSemester, :dep, :program, :semgrp, :import, :additionals );',
		'hide.order'		   => 'update `order` set status = 0 where id = :id;',
		'update.order'         => 'update `order` set status = :status, time = :time, name = :name, user = :user, appendSemester = :appendSemester, dep = :dep, program = :program, semgrp = :semgrp, importsource = :import, additionals = :additionals where id = :id;',
		'delete.all.orders'    => 'truncate table `order`;',
		'get.order.by.teacher' => 'select * from `order` where user = :user;',
		'get.order.by.id'      => 'select * from `order` where id = :id;',
		'get.orders'           => 'select * from `order` order by time desc;'
	];
	
	/**
	 * Methode zum Speichern eines Kursantrages in der Datenbank (back)
	 * @param  array(mixed) $STHData
	 * @access public
	 */
	public static function AddOrder( array $STHData )
	{
		## Statementhandler
	    $STH = Database::GetPrepared( self::$SQL['add.order'] );

	    ## SQL ausführen und Platzhalter ersetzen
	    $STH -> execute( $STHData );
	}

	/**
	 * Methode zum Ändern eines Kursantrages in der Datenbank
	 * @param  array(mixed) $STHData
	 * @access public
	 */
	public static function UpdateOrder( array $STHData )
	{
		## Statementhandler
	    $STH = Database::GetPrepared( self::$SQL['update.order'] );

	    ## SQL ausführen und Platzhalter ersetzen
	    $STH -> execute( $STHData );
	}

	/**
	 * Methode zum Auslesen aller Kursanträge
	 * @return array(mixed)
	 * @access public
	 */
	public static function GetAllOrders()
	{
		## Statementhandler
	  	$STH = Database::GetPrepared( self::$SQL['get.orders'] );

    	## SQL ausführen
	    $STH -> execute();

	    ## Ergebnis zurückliefern
	    return $STH -> fetchAll();
	}

	/**
     * Methode zum Leeren der Tabelle
     * @access public
     */
    public static function DeleteAllOrders()
    {
      ## SQL ausführen
      Database::GetInstance() -> query( self::$SQL['delete.all.orders'] );
    }

    /**
	 * Methode zum Auslesen aller Kursanträge eines Dozenten
	 * @param  int $teacher
	 * @return array(mixed)
	 * @access public
	 */
	public static function GetOrdersByTeacher( $teacher )
	{
		## Statementhandler
	  	$STH = Database::GetPrepared( self::$SQL['get.order.by.teacher'] );

    	## SQL ausführen
	    $STH -> execute( array( ':user' => $teacher ) );

	    ## Ergebnis zurückliefern
	    return $STH -> fetchAll();
	}

	/**
	 * Methode zum Auslesen der Kursantragsdaten anhand seiner ID
	 * @param  int $id
	 * @return array(mixed)
	 * @access public
	 */
	public static function GetOrderById( $id )
	{
		## Statementhandler
	  	$STH = Database::GetPrepared( self::$SQL['get.order.by.id'] );

    	## SQL ausführen
	    $STH -> execute( array( ':id' => $id ) );

	    ## Ergebnis zurückliefern
	    return $STH -> fetch();
	}

	/**
	 * Methode zum Deaktivieren eines Kursantrages
	 * @param  int $id
	 * @access public
	 */
	public static function HideOrder( $id )
	{
		## Statementhandler
	    $STH = Database::GetPrepared( self::$SQL['hide.order'] );

	    ## SQL ausführen und Platzhalter ersetzen
	    $STH -> execute( [ ':id' => $id ] );
	}
}

## Exception-Klasse
class CourseDBException extends Exception {}

?>