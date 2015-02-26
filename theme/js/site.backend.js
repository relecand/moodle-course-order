/* site: backend */
$( document ).ready( function(){

	$( 'input#startdl' ).click( function(){
		$( this ).attr( 'disabled', 'disabled' ).val( '...' );
		var fetch = setInterval( function(){ $.ajax({ 
				url:     'index.php?do=ajax.gettimetable', 
				type:    'POST',
				async:   'true',
				data:    { ttr: 3141528 },
				success: function( data, textStatus, jqXHR )
				{
					button = $( 'input#startdl' );
					data   = JSON.parse( data );
					switch ( data.status )
					{
						case 1:
						{
							button.val( 'Schritt 2/3 - Lade Seminargruppe ' + data.sgActual + ' von ' + data.sgFound + ' ...' );
						} break;
						case 2:
						{
							button.val( 'Schritt 3/3 - Verarbeiten der Stundenpläne ...' );
							clearInterval( fetch );
							location.reload();
						} break;
						case 3:
						{
							//button.removeAttr( 'disabled' ).val( 'Vorgang abgeschlossen. Die Stundenplandaten sollten in wenigen Minuten zur Verfügung stehen.' );
							location.reload();
						} break;
					}
				} 
			});
		}, 2000);
	} );

	$( 'input#apply' ).click( function(){
		window.location.href = window.location.href + '&apply=12';
	} );

	$( 'input#reset' ).click( function(){
		alert( 'Um den Stundenplan für das aktuelle(!) Semester erneut manuell zu laden, bitte manuell die last.access-Datei im Stundenplanddatenverzeichnis löschen.' );
	} );

} );