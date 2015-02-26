<?php 

/**
 * backend.courserequests.view.php
 * Unterseite mit Übersicht über ausstehende Kursanträge
 * @author Robert Leonhardt
 */

## Seitentitel setzen
$HTML -> setTitle( 'Administration - Kursanträge bearbeiten' );

## Kursanträge laden
$orders = CourseDB::GetAllOrders();

## Array mit Anträgen, die relevant sind
$ordersTodo = [];

## Alle Kursanträge durchgehen
foreach ( $orders as $order )
{
	## wichtig sind die, die konkret beantragt sind ..
	if ( intval( $order[ 'status' ] ) == 2 ){
		## Antrag zu den Relevanten hinzufügen
		$ordersTodo[] = $order;
	}
}

## Prüfen, ob relevante Kursanträge vorliegen
if ( count( $ordersTodo ) > 0 ){
	## Liste mit zu erledigenden Kursanträgen erstellen
	$orderList = '<div class="ol t">';
	$orderList .= HTMLUtil::Snippet( 'content.backend.courserequests.olhead.html', [] );

	## Wechselvariable für Farbliche Abgrenzung der einzelnen Elemente
	$oe = true;

	## Alle Kursanträge durchgehen
	foreach ( $ordersTodo as $order )
	{
		## Benutzerdaten des Dozenten ermitteln
		$teacher = UserUtil::GetDataById( $order[ 'user' ] );

		## Seminargruppen herichten (trennen)
		$sgList = explode( ':', Base64::Decode( $order[ 'semgrp' ] ) );

		## Seminargruppenstring
		$sgString = '';

		## Jede Seminargruppe in DIV Verpacken und zu String hinzufügen
		foreach ( $sgList as $sg )
		{
			## Zu String hinzufügen
			$sgString .= '<div class="smgrp">' . $sg . '</div>';
		}

		## Element hinzufügen
		$orderList .= HTMLUtil::Snippet( 'content.backend.courserequests.olitem.html', [
			'oe' => $oe ? 'odd' : 'even',
			'teacher' => $teacher[ 'name' ],
			'name' => $order[ 'name' ],
			'sg' => $sgString,
			'import' => intval( $order[ 'importsource' ] ) > 1 ? 'checked' : 'unchecked',
			'add' => $order[ 'additionals' ] != '' ? 'checked' : 'unchecked'
		] );

		## Wechselvariable ändern
		$oe = !$oe;
	}

	## Liste schließen
	$orderList .= '</div>';
} else {
	## keine Kursanträge zu erledigen ..
	$orderList = '<p>Derzeit liegen keine Kursanträge zur Bearbeitung vor ...</p>';
}

## Inhalt zur Ausgabe hinzufügen
$content[ 'site' ] = HTMLUtil::Snippet( 'content.backend.courserequests.html', [
	'orders' => $orderList
] );

?>