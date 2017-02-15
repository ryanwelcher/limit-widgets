<?php

/**
Plugin Name: Limit Widgets
Plugin URI:
Description: This plugin allows you to limit the number of widgets placed in a sidebar. It also provides a nice to UI to the user to let them know they are at the limit.
Version: 1.0.5
Author: Patrick Rauland, Ryan Welcher
Author URI: http://www.patrickrauland.com, http://www.ryanwelcher.com

Copyright (C) 2012 Patrick Rauland
*/
class LimitWidgets {
	
	/**
	 * Sidebar storage.
	 *
	 * @var array
	 */
	public $sidebars;
	
	/**
	 * LimitWidgets constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 2000 );
	}
	
	/**
	 * Initialize the plugin.
	 */
	public function init() {
		// Get all of the sidebars for this theme.
		global $wp_registered_sidebars;
		foreach ( $wp_registered_sidebars as $key => $value ) {
			$this->sidebars[] = array( 'id' => $value['id'], 'name' => $value['name'] );
		}
		// Enqueue scripts in the wordpress admin.
		add_action( 'admin_enqueue_scripts', array( $this, 'limitw_admin_enqueue_scripts' ) );
		
		// Add submenu for configuring options.
		add_action( 'admin_menu', array( $this, 'limitw_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'options_limitw_page_settings_fields' ) );
		
		// Add the settings link on the plugin screen.
		add_filter( 'network_admin_plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'limitw_filter_action_links' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'limitw_filter_action_links' ) );
	}
	
	/**
	 * Enqueue the scripts we need for the plugin.
	 *
	 * @param string $hook_suffix The page we're on.
	 */
	public function limitw_admin_enqueue_scripts( $hook_suffix ) {
		// If on the widgets page enquque the scripts.
		if ( 'widgets.php' === $hook_suffix ) {
			$postfix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '.js' : '.min.js';
			wp_enqueue_script( 'limit-widgets-js', plugins_url( '/assets/script' . $postfix, __FILE__ ), array(), false, true );
			wp_enqueue_style( 'limit-widgets-css', plugins_url( '/assets/style.css', __FILE__ ) );
			// Bet sidebar data from DB.
			$sidebar_limits = get_option( 'limitw_limits_options' );
			// Check to make sure we have some limitations.
			if ( is_array( $sidebar_limits ) ) {
				foreach ( $sidebar_limits as $key => $value ) {
					// Remove empty values.
					if ( '' === $value ) {
						unset( $sidebar_limits[ $key ] );
					}
				}
				// Pass data to JS file.
				wp_localize_script( 'limit-widgets-js', 'sidebarLimits', $sidebar_limits );
			}
		}
	}
	
	/**
	 * Add the admin menu.
	 */
	public function limitw_admin_menu() {
		// Add a submenu for configuration options.
		add_options_page( 'Limit Widgets Options', 'Limit Widgets', 'manage_options', 'options-limit-widgets.php', array( $this, 'options_limitw_page' ) );
	}
	
	/**
	 * Handles the Limit Widgets settings page.
	 */
	public function options_limitw_page() {
		// Kick the user out if he doesn't have sufficient permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'limit-widgets' ) );
		}
		
		// Show the form.
		echo '<div class="wrap">';
		echo '<div id="icon-options-general" class="icon32"><br></div>';
		echo '<h2>' . esc_html_e( 'Limit Widgets Settings', 'limit-widgets' ) . '</h2>';
		echo '<form method="post" action="options.php"> ';
		settings_fields( 'limitw_limits_options' );
		do_settings_sections( 'limitw_main' );
		submit_button();
		echo '</form>';
		echo '</div>';
		echo '</div>';
	}
	
	/**
	 * Manage the settings fields.
	 */
	public function options_limitw_page_settings_fields() {
		// Add the admin settings and such.
		register_setting( 'limitw_limits_options', 'limitw_limits_options', array( $this, 'limitw_options_validate' ) );
		add_settings_section( 'limitw_main', 'Limit Widgets Main Settings', array( $this, 'limitw_section_text' ), 'limitw_main' );
		
		// Add a setting field for each sidebar.
		if ( is_array( $this->sidebars ) ) {
			foreach ( $this->sidebars as $key => $value ) {
				add_settings_field( 'sidebar-' . $value['id'], $value['name'] . ' limit:', array( $this, 'limitw_setting_string' ), 'limitw_main', 'limitw_main', array( 'sidebar-id' => $value['id'] ) );
			}
		}
		
	}
	
	/**
	 * Add the settings panel to the plugin page
	 *
	 * @param array $links The links for the plugin.
	 *
	 * @return array
	 */
	function limitw_filter_action_links( $links ) {
		$links['settings'] = sprintf( '<a href="%s"> %s </a>',esc_url( admin_url( 'options-general.php?page=options-limit-widgets.php' ) ), esc_html__( 'Settings', 'plugin_domain' ) );
		
		return $links;
	}
	
	/**
	 *  Print some text so the user knows what to do in this section.
	 */
	public function limitw_section_text() {
		if ( is_array( $this->sidebars ) ) {
			echo '<p>' . esc_html__( 'Just type the maximum number of widgets you would like in each sidebar. If you don&#39;t want to set a maximum then just leave it blank.', 'limit-widgets' ) . '</p>';
		} else {
			echo '<p>' . esc_html__( 'test', 'limit-widgets' ) . '</p>';
		}
	}
	
	/**
	 * Print out the form elements for our options.
	 *
	 * @param array $args Array containing the settings args.
	 */
	public function limitw_setting_string( $args ) {
		$sidebar_id = $args['sidebar-id'];
		$options   = get_option( 'limitw_limits_options' );
		$val       = isset( $options[ $sidebar_id ] ) ? $options[ $sidebar_id ] : '';
		echo "<input name='limitw_limits_options[" . esc_attr( $sidebar_id ) . "]' type='number' min='0' max='999' value='" . esc_attr( $val ) . "'/>";
	}
	
	/**
	 * Validate our options.
	 *
	 * @param array $input Options input.
	 *
	 * @return mixed
	 */
	public function limitw_options_validate( $input ) {
		
		// Validate the limit for each sidebar.
		foreach ( $this->sidebars as $key => $value ) {
			/**
			 * Right now these are evaluated as boolean (0 or 1) but these need to be evaluated as integer
			 *
			 * @todo
			 */
			$validated_array[ $value['id'] ] = trim( $input[ $value['id'] ] );
			
			if ( ( ! is_numeric( $validated_array[ $value['id'] ] ) ) || ( $validated_array[ $value['id'] ] < 0 ) ) {
				// Set an empty string.
				$validated_array[ $value['id'] ] = '';
			} else {
				// After we've weeded out the empty values & non numbers now we can type cast to an integer.
				$validated_array[ $value['id'] ] = (int) $validated_array[ $value['id'] ];
			}
		}
		
		return $validated_array;
	}
	
}


/**
 * Initialize the plugin
 */
new LimitWidgets();
