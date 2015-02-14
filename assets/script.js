/*global sidebarLimits*/
jQuery( function( $ ) {
	var LIMIT_WIDGETS = {


		realSidebars : $( '#widgets-right div.widgets-sortables' ),

		availableWidgets : $( '#widget-list' ).children( '.widget' ),

		wpStandardAddWidget : null,

		checkLength : function( sidebar, delta  ) {
			var sidebarId = sidebar.id,
				widgets,
				notFullSidebars;


			if ( undefined === sidebarLimits[sidebarId] ) {
				return;
			}

			// This is a limited sidebar
			// Find out how many widgets it already has
			widgets = $( sidebar ).sortable( 'toArray' );

			//moving the class up a level and changing the name to be display only
			$( sidebar ).parent().toggleClass( 'sidebar-full-display', sidebarLimits[sidebarId] <= widgets.length + (delta || 0) );
			$( sidebar ).parent().toggleClass( 'sidebar-morethanfull-display', sidebarLimits[sidebarId] < widgets.length + (delta || 0) );

			//still adding the original class to keep the goodness below working properly
			$( sidebar ).toggleClass( 'sidebar-full', sidebarLimits[sidebarId] <= widgets.length + (delta || 0) );

			notFullSidebars = $( 'div.widgets-sortables' ).not( '.sidebar-full' );

			this.availableWidgets.draggable( 'option', 'connectToSortable', notFullSidebars );
			this.realSidebars.sortable( 'option', 'connectWith', notFullSidebars );
		},

		init : function() {

			var that = this;

			this.wpStandardAddWidget = wpWidgets.addWidget;


			this.realSidebars.map(function () {
				that.checkLength(this);
			});

			// Update when dragging to this (sort-receive)
			// and away to another sortable (sort-remove)
			this.realSidebars.bind('sortreceive sortremove', function (event, ui) {
				that.checkLength(this);
			});

			// Update when dragging back to the "Available widgets" stack
			this.realSidebars.bind('sortstop', function (event, ui) {
				if (ui.item.hasClass('deleting')) {
					that.checkLength(this, -1);
				}
			});

			// Update when the "Delete" link is clicked
			$('a.widget-control-remove').live('click', function () {
				that.checkLength($(this).closest('div.widgets-sortables')[0], -1);
			});

			wpWidgets.addWidget = function (chooser) {

				var sidebarId = chooser.find('.widgets-chooser-selected').data('sidebarId'),
					sidebar = $('#' + sidebarId);

				//if there is no added class - act normally
				if (false === sidebar.hasClass('sidebar-full')) {
					wpStandardAddWidget(chooser);

					//recall the map functionalty to update the view
					realSidebars.map(function () {
						that.checkLength(this);
					});
				}
			}
		}
	};

	//start the show
	LIMIT_WIDGETS.init();
});