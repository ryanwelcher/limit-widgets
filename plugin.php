<?php
/*
Plugin Name: Limit Widgets
Plugin URI: 
Description: This plugin allows you to limit the number of widgets placed in a sidebar. It also provides a nice to UI to the user to let them know they are at the limit.
Version: 1.0.1
Author: Patrick Rauland
Author URI: http://www.patrickrauland.com

Copyright (C) 2012 Patrick Rauland

*/

class LimitWidgets
{
	public $sidebars;

	public function __construct()
	{
		add_action( 'init', array( &$this, 'init' ), 2000 );
	}

	public function init()
	{
		//intialize the plugin

		//get all of the sidebars for this theme
		global $wp_registered_sidebars;
		foreach ($wp_registered_sidebars as $key => $value) 
		{
			$this->sidebars[] = array( "id"=>$value['id'], "name"=>$value['name'] );
		}

		//enqueue scripts in the wordpress admin
		add_action( 'admin_enqueue_scripts', array( &$this, 'limitw_admin_enqueue_scripts' ) );

		//add submenu for configuring options
		add_action('admin_menu', array(&$this, 'limitw_admin_menu'));
		add_action('admin_init', array(&$this, 'options_limitw_page_settings_fields'));

		
		//add the settings link on the plugin screen
		add_filter('network_admin_plugin_action_links_'.  plugin_basename( __FILE__ ) ,  array( &$this,'limitw_filter_action_links')); 
		add_filter('plugin_action_links_' .  plugin_basename( __FILE__  )  , array(&$this,'limitw_filter_action_links')); 

	}

	public function limitw_admin_enqueue_scripts( $hook_suffix )
	{
		//if on the widgets page enquque the scripts
		if ( $hook_suffix == 'widgets.php' ) {
			wp_enqueue_script( 'limit-widgets-js', plugins_url( "/assets/script.js" , __FILE__ ), array(), false, true );
			wp_enqueue_style( 'limit-widgets-css', plugins_url( "/assets/style.css" , __FILE__ ) );

			//get sidebar data from DB
			$sidebarLimits = get_option('limitw_limits_options');

			// check to make sure we have some limitations
			if ( is_array( $sidebarLimits ) ) {
				// print_r($sidebarLimits); exit();
				foreach ( $sidebarLimits as $key => $value ) 
				{
					// remove empty values
					if($value==="")
					{
						unset($sidebarLimits[$key]);
					}
				}

				// pass data to JS file
				wp_localize_script( 'limit-widgets-js', 'sidebarLimits', $sidebarLimits );
			}
		}
	}

	public function limitw_admin_menu()
	{
		//add a submenu for configuration options

		add_options_page('Limit Widgets Options', 'Limit Widgets', 'manage_options', 'options-limit-widgets.php', array($this, 'options_limitw_page'));
	}

	public function options_limitw_page() 
	{
		//print out the content on the limit widgets settings page

		//kick the user out if he doesn't have sufficient permissions
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		//show the form
		echo '<div class="wrap">';
		echo '<div id="icon-options-general" class="icon32"><br></div>';
		echo '<h2>'._('Limit Widgets Settings','limit-widgets') . '</h2>';
		echo '<form method="post" action="options.php"> ';
		settings_fields( 'limitw_limits_options' );
		do_settings_sections( 'limitw_main' );
		submit_button(); 
		echo '</form>';
		echo '</div>';
		echo '</div>';
	}

	public function options_limitw_page_settings_fields()
	{
		// add the admin settings and such
		
		register_setting( 'limitw_limits_options', 'limitw_limits_options', array( &$this, 'limitw_options_validate') );
		add_settings_section('limitw_main', 'Limit Widgets Main Settings', array( &$this, 'limitw_section_text') , 'limitw_main');

		//add a setting field for each sidebar
		if( is_array( $this->sidebars ) ) {
			foreach ($this->sidebars as $key => $value) {
				add_settings_field('sidebar-'.$value['id'], $value['name'].' limit:', array( &$this, 'limitw_setting_string'), 'limitw_main', 'limitw_main', array( 'sidebar-id'=>$value['id'] ));
			}
		}
	
	}



	/**
	 * Add the settings panel to the plugin page
	 * @param array The links for the plugin
	 * @return array
	 */
	function limitw_filter_action_links($links) {
		$links['settings'] = sprintf( '<a href="%s"> %s </a>', admin_url( 'options-general.php?page=options-limit-widgets.php' ), __( 'Settings', 'plugin_domain' ) );
		return $links;
	}




	public function limitw_section_text() 
	{
		//print some text so the user knows what to do in this section
		if( is_array( $this->sidebars ) ) {
			echo "<p>" . __("Just type the maximum number of widgets you would like in each sidebar. If you don&#39;t want to set a maximum then just leave it blank.", "limit-widgets") . "</p>";
			}
		else{
			echo "<p>" . __("test", "limit-widgets") . "</p>";
		}
	}

	public function limitw_setting_string($args) 
	{
		//print out the form elements for our options
		
		$sidebarId = $args['sidebar-id'];
		$options = get_option('limitw_limits_options');
		echo "<input name='limitw_limits_options[".$sidebarId."]' type='number' min='0' max='999' value='".$options[$sidebarId]."'/>";
	}

	
	public function limitw_options_validate($input) 
	{
		// validate our options

		//validate the limit for each sidebar
		foreach ($this->sidebars as $key => $value) 
		{
			//right now these are evaluated as boolean (0 or 1)
			//but these need to be evaluated as integers
			//TODO
			$validatedArray[$value['id']] = trim($input[$value['id']]);
			
			if( (!is_numeric($validatedArray[$value['id']])) || ($validatedArray[$value['id']] < 0)) 
			{	
				//set an empty string
				$validatedArray[$value['id']] = "";
			}//if
			else
			{
				//after we've weeded out the empty values & non numbers now we can type cast to an integer
				$validatedArray[$value['id']] = (int) $validatedArray[$value['id']];
			}

		}

		return $validatedArray;
	}

}



/**
 * Initialize the plugin
 */
new LimitWidgets();

//That's it! Thank you very much and good night.