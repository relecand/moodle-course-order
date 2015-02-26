<?php 

/**
 * HTMLDocument.php
 * Klasse für die Verarbeitung von HTML-Dokumenten
 * @author Robert Leonhardt
 */
class HTMLDocument extends HTML 
{
	/**
	 * benötigte Regex-Patterns
	 * @var    array(string)
	 * @access private
	 */
	private static $Regex = [ 'includeHeadFile.filetype' => '/([a-z0-9\/-_\.]+)\.(css|js)$/ui' ];

	/**
	 * intern benötigte Templates
	 * @var    array(string)
	 * @access private
	 */
	private static $Template = [ 'css' => 'include.css.html', 
								 							 'js'  => 'include.js.html' ];

	/**
	 * Variable mit zu inkludierenden Dateien
	 * @var    array(string)
	 * @access private
	 */
	private $includes = [ 'css' => [], 'js' => [] ];

	/**
	 * Konstruktor zum Übernehmen des Templates und sonstiger Parameter
	 * @access public
	 */
	public function __construct()
	{
		## Elternkonstruktor anwerfen
		parent::__construct( HTML_DOCUMENT_DEFAULT, false );
	}

	/**
	 * Methode zum Setzen des Titels
	 * @param  string $title
	 * @access public
	 */
	public function setTitle( $title )
	{
		## Titel im Elternobjekt registrieren
		$this -> replace( 'head.title', $title . HTML_DOCUMENT_TITLE_APPEND );
	}

	/**
	 * Methode zum Hinzufügen von Include-Dateien
	 * @param  string $filename
	 * @access public 
	 */
	public function includeHeadFile( $file )
	{
		## $filename auf Dateityp prüfen
		preg_match( self::$Regex['includeHeadFile.filetype'], $file, $filetype );

		## Exception, wenn Datei nicht ins Format *.css oder *.js passt
		if ( empty( $filetype ) ){
			throw new HTMLException( 'Cannot include file "' . SELF_URL . $file . '". Wrong file type?', 2001 );
		}

		## Dateityp in Variable überführen (array -> string)
		$filetype = $filetype[2];

		## Exception, wenn Datei nicht existiert
		if ( !file_exists( ROOT . $file ) ){
#			throw new HTMLEXception( 'Cannot include file "' . THIS_URL . $file . '". File not found.', 2002 );
		}

		## Datei im Array anmelden
		$this -> includes[ $filetype ][] = $file;

		## doppelte Dateien aus dem Array entfernen
		$this -> includes[ $filetype ] = array_unique( $this -> includes[ $filetype ] );

		## String initialisieren, der als Platzhalter genutzt wird
		$content = null;

		## Alle Dateien des angegebenen Typs zu einem String zusammenführen (HTML formatiert)
		foreach ( $this -> includes[ $filetype ] as $file )
		{
			$content .= HTMLUtil::Snippet( self::$Template[ $filetype ], [ 'path' => $file /*. '?nocache=' . TIME_NOW*/ ] ) . PHP_EOL;
		}

		## Platzhalter setzen
		$this -> replace( 'head.include.' . $filetype, $content );
	}
}

?>