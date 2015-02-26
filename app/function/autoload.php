<?php 

/**
 * autoload.php
 * Autoloaderfunktion zum automatischen Laden von angeforderten Klassen (max. ein Unterverzeichnis)
 * @author Robert Leonhardt
 *
 * @param  string $class
 * @param  array(string) $packages
 * @return string
 */
function autoload( $class, array $packages = array() )
{
  /*
  ## Alle Pakete durchgehen
  foreach ( $packages as $package )
  {
    ## Dateipfad erstellen
    $path = SYSTEM . 'class/' . $package . '/' . $class . '.class.php';

    ## Pfad zurückgeben (wird von der anon.-Funktion eingebunden), falls Datei existiert
    if ( file_exists( $path ) ){
      return $path;
    }
  }
  */
  ## Klassenverzeichnis nach angeforderter Klasse durchsuchen
  foreach ( glob( SYSTEM . 'class/{,*/}' . $class . '.php', GLOB_BRACE ) as $file )
  {
    ## Pfad zurückliefern
    return $file;
  }

  ## Error ausgeben, Klasse nicht gefunden
  trigger_error( 'Class ' . $class . ' not found.', E_USER_ERROR );
}

?>