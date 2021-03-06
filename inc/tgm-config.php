<?php
/**
 * TGM Plugin Activation Configuration
 *
 *
 * @package aquarella-lite
 */

function aquarella_require_plugins() {
 
 	//Plugins
    $plugins = array( 
    	array(
			'name'     				=> esc_html( 'Kirki', 'aquarella-lite' ),
			'slug'     				=> 'kirki',
			'required' 				=> false,
			'version' 				=> '',
			'force_activation' 		=> false,
			'force_deactivation' 	=> false,
			'external_url' 			=> '',
		)
	);

    //Configs
    $config = array( 
    	'id'           => 'aquarella-lite',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.			
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
    );
 
    tgmpa( $plugins, $config );
 
}

add_action( 'tgmpa_register', 'aquarella_require_plugins' );