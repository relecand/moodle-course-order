<?php 
require_once( CONFIG . 'html.config.php' ); 

/**
 * HTML.php
 * Klasse zur allgemeinen Templatebehandlung
 * @author Robert Leonhardt
 */
class HTML
{
	/**
	 * Quelltext, unbearbeitet
	 * @var    string
	 * @access private
	 */
	private $source;

	/**
	 * Platzhalter
	 * @var    array(mixed)
	 * @access private
	 */
	private $placeholder = [];

	/**
	 * Benötigte Regex-Anweisungen
	 * @var    array (mixed)
	 * @access private
	 */
	private static $Regex = [ 'placeholder' => '/{([a-z0-9-\.\ ]+)}/ui',
							  'remove'      => [ 'emptylines' => '/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/u', 
							  					 /* TODO 'whitespace' => '' */ ] ];

	/**
	 * Konstruktormethode zum Laden des Templates
	 * @param  string $template
	 * @param  bool $allowString
	 * @access public
	 */
	public function __construct( $template, $allowString = true )
	{
		## Exception, wenn Datei erfordert, aber nicht existent
		if ( !$allowString and !file_exists( HTML . $template ) ){
			throw new HTMLException( 'Required template "' . $template . '" not found', 1001 );
		}

		## Template auslesen oder, falls Datei nicht existent, Parameter $template als String übernehmen
		$this -> source = file_exists( HTML . $template ) ? file_get_contents( HTML . $template ) : (string)$template;
	}

	/**
	 * Methode zum Implementieren von Platzhaltern - möglich: $obj -> placeholder( $key -> $value ) und $obj -> placeholder( [ $key1 -> $value1, ... ] )
	 * @param  mixed $placeholder
	 * @param  string $value
	 * @access public
	 */
	final public function replace( $placeholder, $value = null )
	{
		## ggfs. Einzelangabe in Array überführen
		if ( !is_array( $placeholder ) ){
			$placeholder = [ $placeholder => $value ];
		}

		## Platzhalter einzeln in Objektarray einfügen
		foreach ( $placeholder as $key => $value )
		{
			$this -> placeholder[ $key ] = (string)$value;
		}
	}

	// /**
	//  * Methode zum Ausgeben aller Platzhalter
	//  * @access public
	//  */
	// final public function printPlaceholder()
	// {
	// 	var_dump( $this -> placeholder );
	// }

	/**
	 * Methode zum Verarbeiten des Quelltextes für die letztendliche Ausgabe
	 * @return string
	 * @access public
	 */
	final private function prepare()
	{
		## Kopie des Quelltextes in eine Variable überführen
		$html = $this -> source;

		## Template nach Platzhaltern durchsuchen
		preg_match_all( self::$Regex['placeholder'], $html, $foundPlaceholders, PREG_SET_ORDER );

		## Alle gefundenen Platzhalter im Template durchgehen
		foreach ( $foundPlaceholders as $foundPlaceholder )
		{
			## Füllwert ermitteln
			$content = isset( $this -> placeholder[ $foundPlaceholder[1] ] ) ? $this -> placeholder[ $foundPlaceholder[1] ] : null;

			## Platzhalter mit gefundenem Füllwert oder null füllen
			$html 	 = str_replace( $foundPlaceholder[0], $content, $html );
		}

		## Leerzeilen entfernen
		$html = preg_replace( self::$Regex['remove'], PHP_EOL, $html );

		// QUELLTEXT FORMATIEREN

		## Bearbeiteten Wert zurückliefern
		return $html;
	}

	/**
	 * Methode für die direkte Objektausgabe (echo $obj)
	 * @return string
	 * @access public
	 */
	final public function __toString()
	{
		## Bearbeiteten Quelltext zurückliefern, dieser wird automatisch ausgegeben
		return $this -> prepare();
	}
}

?>