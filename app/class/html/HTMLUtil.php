<?php 

/**
 * HTMLUtil.php
 * Klasse mit Hilfsmethode zur Templateverarbeitung 
 * @author Robert Leonhardt
 */
abstract class HTMLUtil
{
	/**
	 * Methode zum Auswerten von HTML-Elementen "on the fly"
	 * @param  string $template
	 * @param  array  $placeholder
	 * @return string
	 * @access public
	 */
	public static function Snippet( $template, array $placeholder = null )
	{
		## Objekt erstellen
		$HTML = new HTML( $template );

		## URL hinzufügen
		$placeholder[ 'self.url' ] = SELF_URL;

		## Platzhalter platzieren
		$HTML -> replace( $placeholder );

		## Objekt (als String) zurückgeben
		return (string)$HTML;
	}
}

?>