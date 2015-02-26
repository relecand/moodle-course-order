/* site: order-form */
$( document ).ready( function(){
	var hash 	   = window.location.hash.replace( '#!', '' );
	var defaultTab = 'cn';

	if ( hash == '' ){
		hash = defaultTab;
	}

	if ( hash == 'cns' ){
		$( 'p#success' ).show(0).delay(5000).slideUp(100);
	}

	setVisibleContent( hash );
	$( 'div.content div.menu > div.item, div.content *.itemelement' ).click( function(){
		setVisibleContent( $( this ).attr( 'id' ) );
	} );

	activateAccItem( $( 'div.content div.a > div.ai:first + div.ai' ) );
	$( 'div.content div.a > div.ai' ).click( function(){
		activateAccItem( $( this ) );
	} );

	$( 'select[name=co], select[name=cfoc]' ).change( function(){
		if ( $( this ).val() == 'false' ){
			$( 'input[name=fcn]' ).val( '' );
			$( 'input[name=cn]' ).val( '' ).slideUp(100);
			$( 'input.s' ).slideUp(0);
			$( 'p#missingname' ).slideDown(0);
			$( 'input[name=fic]' ).val( '' );
		} else {
			var course   = $( 'select[name=' + $( this ).attr( 'name' ) + '] option:selected' ).text();
			var id       = $( 'select[name=' + $( this ).attr( 'name' ) + '] option:selected' ).val();
			var semester = $( 'input[name=fsm]' ).val();
			if ( $( this ).attr( 'name' ) == 'cfoc' ){
				$( 'input[name=fic]' ).val( id );
			} else {
				$( 'input[name=fic]' ).val( '' );
			}
			$( this ).parents( 'div' ).slideDown(100);
			$( 'input[name=fcn]' ).val( course );
			$( 'input[name=cn]' ).slideDown(100).val( course + ' - ' + semester );
			$( 'p#missingname' ).slideUp(0);
			$( 'input.s' ).slideDown(0);
		}
	} );

	$( 'input[id=cni]' ).keyup( function(){
		//alert('bla');
		if ( $( this ).val() == 'false' ){
			$( 'input[name=fcn]' ).val( '' )
			$( 'input[name=cn]' ).val( '' ).slideUp(100);
			$( 'input.s' ).slideUp(0);
			$( 'p#missingname' ).slideDown(0);
		} else {
			var course   = $( this ).val(); //$( 'select[name=' + $( this ).attr( 'name' ) + '] option:selected' ).text();
			var semester = $( 'input[name=fsm]' ).val();
			$( this ).parents( 'div' ).slideDown(100);
			$( 'input[name=fcn]' ).val( course );
			$( 'input[name=cn]' ).slideDown(100).val( course + ' - ' + semester );
			$( 'p#missingname' ).slideUp(0);
			$( 'input.s' ).slideDown(0);
		}
	} );

	$( 'textarea[name=ad]' ).keyup( function(){
		if ( !$( this ).val() ){
			$( 'input[name=ca]' ).val( '' ).slideUp(100);
		} else {
			$( 'input[name=ca]' ).slideDown(100).val( $( this ).val() );
		}
		$( 'input[name=fad]' ).val( $( this ).val() );
	} );

	$( 'select[name=cs]' ).change( function(){
		//alert( $(this).find( ':selected' ).attr( 'id' ) );
		selectedprogram = $(this).find( ':selected' ).attr( 'value' );
		selectedprogramtype = $(this).find( ':selected' ).attr( 'id' );
		$.post( 'index.php?do=ajax.getcourses', 
			{ program: selectedprogram },
			function( data, textStatus, jqXHR )
			{
				//alert(data);
				$( 'select[name=co]' ).html( data );
				$( 'input[name=cn]' ).val( '' ).slideUp(100);
			} 
		);
		$( 'input[name=fcs]' ).val( selectedprogram );
		if ( selectedprogramtype == 3 ){
			$( '#cns' ).hide( 0 );
			$( '#cni' ).show( 0 );
		} else {
			$( '#cni' ).hide( 0 );
			$( '#cns' ).show( 0 );
		}
		$( '#csg' ).val( selectedprogram );
	} );

	/*$( 'select[name=cfoc]' ).change( function(){
		courseid = $(this).find( ':selected' ).attr( 'value' );

		$( 'input[name=fcs]' ).val( courseid );
	} );*/

	$( 'div.right div#sg .smgrp' ).click( function(){
		if ( $( this ).hasClass( 'all' ) ){
			if ( $( this ).parents( 'div.tc:first' ).siblings( 'div.w60:first' ).is( ':empty' ) ){
				$( this ).removeClass( 'all' );
			}
		}
		var smgrp 	 = $( this ).html();
		var selected = $( this ).hasClass( 'selected' );
		var all 	 = $( this ).hasClass( 'all' );
		if ( !$( this ).hasClass( 'disabled' ) ){
			if ( !selected ){
				if ( all ){
					$( this ).parents( 'div.tc:first' ).siblings( 'div.w60:first' ).children( 'div.smgrp' ).each( function(){
						addToSemGroupList( $( this ).html() );
						$( this ).toggleClass( 'disabled' );
					} );
				} else {
					addToSemGroupList( $( this ).html() );
				}
				$( this ).addClass( 'selected' );
			} else {
				if ( all ){
					$( this ).parents( 'div.tc:first' ).siblings( 'div.w60:first' ).children( 'div.smgrp' ).each( function(){
						removeFromSemGroupList( $( this ).html() );
						$( this ).toggleClass( 'disabled' ).removeClass( 'selected' );
					} );
				} else {
					removeFromSemGroupList( smgrp );
				}
				$( this ).removeClass( 'selected' );
			}
		}
		var smgrps = '';
		$( 'div.left div#sg div div.help' ).children( 'div.smgrp' ).each( function(){
			smgrps = smgrps + $( this ).html() + ';';
		} );
		$( 'input[name=fsg]' ).val( smgrps );
	} );

	$( 'form#course-request' ).submit( function(){
		if ( !$( 'input[name=fcn]' ).val() ){
			alert( 'Bitte geben Sie eine Kursbezeichnung ein.' );
			event.preventDefault();
		}
	} );

	function addToSemGroupList( semgroup )
	{
		var listElement   = $( 'div.left div#sg div div.help' );
		var alreadyInList = false;
		listElement.children( 'div.smgrp' ).each( function(){
			if ( $( this ).html() == semgroup ){
				alreadyInList = true;
			}
		} );
		if ( !alreadyInList ){
			listElement.append( '<div class="smgrp selected">' + semgroup + '</div>' );
		}
	}

	function removeFromSemGroupList( semgroup )
	{
		$( 'div.left div#sg div div.help div.smgrp' ).filter( ":contains('" + semgroup + "')" ).remove();
	}

	function setVisibleContent( element )
	{
		if (  $( 'div.menu > div.item#' + element ).length == 0 ){
			element = defaultTab;
		}
		$( 'div.menu > div.item.active, div.content.active' ).removeClass( 'active' );
		$( 'div.menu > div.item#' + element + ', div.content#' + element ).addClass( 'active' );
		window.location.hash = '!' + element;
	}

	function activateAccItem( element )
	{
		if ( !element.hasClass( 'active' ) ){
			$( 'div.a > div.ai.active div.icon' ).removeClass( 'checkbox-checked' ).addClass( 'checkbox-unchecked' );
			$( 'div.a > div.ai.active div.ac' ).slideUp( 500 );
			$( 'div.a > div.ai.active' ).removeClass( 'active' );
			element.addClass( 'active' );
			$( 'div.a > div.ai.active div.ac' ).slideDown( 500 );
			$( 'div.a > div.ai.active div.icon' ).removeClass( 'checkbox-unchecked' ).addClass( 'checkbox-checked' );
		}
	}
} );