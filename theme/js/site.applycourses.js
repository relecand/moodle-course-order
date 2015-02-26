/* site: backend */
$( document ).ready( function(){

	$( 'div.sl div.title' ).click( function(){
		$( this ).siblings( 'div.content' ).slideToggle( 200 );
	} );

	$( '*.dco' ).click( function(){
		if ( confirm( 'Achtung: Entfernte Kursanträge können nicht wiederhergestellt werden und müssen ggf. manuell neubeantragt werden.' ) ){
			window.location.href = $( this ).attr( 'data-url' );
		}
	} );

} );