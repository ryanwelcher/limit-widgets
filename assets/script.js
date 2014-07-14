jQuery( function( $ ) {
	
	// this variable gets created using the wp_localize_script function in plugin.php
	// I just listed it here for completeness
	// var sidebarLimits;

	var realSidebars = $( '#widgets-right div.widgets-sortables' );
	var availableWidgets = $( '#widget-list' ).children( '.widget' );

	var checkLength = function( sidebar, delta ) {
		var sidebarId = sidebar.id;
		if ( undefined === sidebarLimits[sidebarId] ) {
			return;
		}

		// This is a limited sidebar
		// Find out how many widgets it already has
		var widgets = $( sidebar ).sortable( 'toArray' );

		//moving the class up a level and changing the name to be display only
		$( sidebar ).parent().toggleClass( 'sidebar-full-display', sidebarLimits[sidebarId] <= widgets.length + (delta || 0) );
		$( sidebar ).parent().toggleClass( 'sidebar-morethanfull-display', sidebarLimits[sidebarId] < widgets.length + (delta || 0) );

		//still adding the original class to keep the goodness below working properly
		$( sidebar ).toggleClass( 'sidebar-full', sidebarLimits[sidebarId] <= widgets.length + (delta || 0) );

		var notFullSidebars = $( 'div.widgets-sortables' ).not( '.sidebar-full' );

		availableWidgets.draggable( 'option', 'connectToSortable', notFullSidebars );
		realSidebars.sortable( 'option', 'connectWith', notFullSidebars );
	}

	// Check existing sidebars on startup
	realSidebars.map( function() {
		checkLength( this );
	} );

	// Update when dragging to this (sort-receive)
	// and away to another sortable (sort-remove)
	realSidebars.bind( 'sortreceive sortremove', function( event, ui ) {
		checkLength( this );
	} );

	// Update when dragging back to the "Available widgets" stack
	realSidebars.bind( 'sortstop', function( event, ui ) {
		if ( ui.item.hasClass( 'deleting' ) ) {
			checkLength( this, -1 );
		}
	} );

	// Update when the "Delete" link is clicked
	$( 'a.widget-control-remove' ).live( 'click', function() {
		checkLength( $( this ).closest( 'div.widgets-sortables' )[0], -1 );
	} );
} );