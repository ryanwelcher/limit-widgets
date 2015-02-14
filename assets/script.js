/*global sidebarLimits*/
jQuery( function( $ ) {
	var LIMIT_WIDGETS = {


		realSidebars : $( '#widgets-right div.widgets-sortables' ),

		availableWidgets : $( '#widget-list' ).children( '.widget' ),

		wpStandardAddWidget : null,

		add_attempt_count : 0,

		attempt_messages : ['Widget Limit Reached.', 'Yup, still full.', 'Really?', 'Ok. This time you can add a new one.', 'Just kidding, it\'s full.' ],

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
					sidebar = $('#' + sidebarId),
					limit_message;

				//if there is no added class - act normally
				if (false === sidebar.hasClass('sidebar-full')) {
					wpStandardAddWidget(chooser);

					//recall the map functionalty to update the view
					realSidebars.map(function () {
						that.checkLength(this);
					});
				} else {
					sidebar.append('<div id="limit-message" class="limit-reached-message">' + that.attempt_messages[ that.add_attempt_count ] + '</div>');

					if( that.add_attempt_count < that.attempt_messages.length - 1 ) {

						that.add_attempt_count++;
					} else {
						that.add_attempt_count = 0;
					}

					limit_message = $('#limit-message');
					limit_message.delay('1000').fadeOut('3000', function(){
						limit_message.remove();
					});
				}
			};
		}
	};
	//start the show
	LIMIT_WIDGETS.init();
});