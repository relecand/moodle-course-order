<?php 

/**
 * main.controller.php
 * Haupt-VIEW-Controller
 * @auhtor Robert Leonhardt
 */

## Startseite - Standardseite, die ohne Übergabe einer gültigen Seite aufgerufen wird
define( 'SITE_DEFAULT',        $Session -> isAdmin() ? 'backend' : 'applycourses' );
## Aufgerufene Seite
define( 'SITE_REQUESTED',      Request::DATA( 'view', SITE_DEFAULT ) );
## Aufgerufene Seite (als Pfad)
define( 'SITE_REQUESTED_PATH', CONTROLLER . 'view/' . SITE_REQUESTED . '.view.php' );


## HTML-Dokument instanziieren
$HTML = new HTMLDocument();


## Array mit Seiten, die aufgerufen werden können (alle anderen Requests landen auf der oben definierten Startseite)
$sites = array(
  ## Diese Einträge niemals nicht ändern
  SITE_DEFAULT,
  ## Die hier aufgeführten Seiten können aufgerufen werden, somit können einzelne Seiten teilweise deaktiviert werden usw.
  'login', 'backend', 'order', 'main', 'webservicetest', 'applycourses'
);


## Hauptmenü - Links zusammenfügen
{
  ## Array mit Menü-Elementen
  $menu = [];

  ## Admin-Link (wird nur eingefügt, wenn Benutzer Admin ist)
  if ( $Session -> isAdmin() ){
    $menu[] = [ 'title' => 'Administration',  'href' => SELF_URL . '?view=backend&sitecourserequests' ];
    ##$menu[] = [ 'title' => 'Webservice Test', 'href' => SELF_URL . '?view=webservicetest' ];
  }

  ## Logout-Link (wird nur angezeigt, wenn Benutzer angemeldet)
  if ( $Session -> getUser() != null ){
    $menu[] = [ 'title' => 'Meine Semesterkurse', 'href' => SELF_URL . '?view=applycourses' ];
    $menu[] = [ 'title' => 'Kurs beantragen',     'href' => SELF_URL . '?view=order' ];
    //$menu[] = [ 'title' => 'Meine Anträge',   'href' => SELF_URL . '?view=orders' ];
    $menu[] = [ 'title' => 'Abmelden',            'href' => SELF_URL . '?do=logout' ];
  }

  ## Link zu Moodle (wird IMMER angezeigt)
  ##$menu[] = [ 'title' => 'Zurück zu Moodle', 'href' => 'http://elearning.tfh-wildau.de/my/' ];
}

## Hauptmenü - Menü generieren
{
  ## String mit HTML-Code für Menü (nicht anfassen!)
  $menuString = '';

  ## Alle Elemente durchgehen
  foreach ( $menu as $item )
  {
    $menuString .= HTMLUtil::Snippet( 'main.nav.link.html', [ 'title' => $item['title'], 'href' => $item['href'] ] );
  }
}


## Hauptmenü - String einfügen
$HTML -> replace( 'navigation', $menuString );


## Auf Login-Seite verweisen, wenn nicht eingeloggt
if ( $Session -> getUser() == null and SITE_REQUESTED != 'login' ){
  Request::Send( 'login', SEND_VIEW, [ 'nli' => 1 ] );
}


## Auf Fehlerseite weiterleiten, wenn angeforderte Seite nicht existiert
if ( !file_exists( SITE_REQUESTED_PATH ) or !in_array( SITE_REQUESTED, $sites ) ){
   Request::Send( SITE_DEFAULT, SEND_VIEW ); #echo '"' . SITE_REQUESTED_PATH . '" not found.'; exit;
}
## Seite einbinden
require_once( SITE_REQUESTED_PATH );


## Stylesheet einfügen
$HTML -> includeHeadFile( 'theme/main.css' ); 


    /* ANFANG!
        #Kleiner Ablauf um Aktualität des Stylesheets im Browser zu gewährleisten
        $actualCSS        = glob( ROOT . 'theme/main.*.css' )[0];
        preg_match( '/[0-9]+/', $actualCSS, $actualCSSVersionArray );
        $actualCSSVersion = (int)$actualCSSVersionArray[0];
        copy( $oldCSS = ROOT . 'theme/main.' . $actualCSSVersion . '.css', 
              $newCSS = ROOT . 'theme/main.' . ( $actualCSSVersion + 1 ) . '.css' );
        unlink( $oldCSS );
        $HTML -> includeHeadFile( 'theme/main.' . ( $actualCSSVersion + 1 ) . '.css' );
    ## ENDE! */


## Ausgabe
echo $HTML;
// JJ D 000 39 00 00 98 91 82 316
?>