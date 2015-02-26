<?php 

/**
 * Datenbankkonfiguration
 * Bitte auf sichere Passwörter und seperate DB-Benutzer achten.
 * @author Robert Leonhardt
 */

## Datenbanktyp
define( 'DB_TYPE', 'mysql' );

## Datenbankverbindung
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_PORT', 3306 );

## Datenbankbenutzer
define( 'DB_USER', 'root' );
define( 'DB_PASS', 'geheim' );

## Datenbank
define( 'DB_NAME', 'ccthmoodle' );

## Conntection-String für PDO-Object
define( 'DB_PDO_CONNECTIONSTRING', DB_TYPE . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME );

?>