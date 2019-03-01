<?php

// Exit if loaded from outside of WP
if ( !defined( 'ABSPATH' ) ) exit;

// HELPER FUNCTIONS BEGIN HERE ///////////////////////////////////////////////////////////
// Return the page slug
function swt_sawyer_page_slug()
{
	return 'swt_sawyer_wp';
}

// Return the option group name
function swt_sawyer_option_group()
{
	return 'sawyer_settings';
}

function swt_sawyer_option_name()
{
	return 'swt_sawyer_options';
}

// Return the fields
function swt_sawyer_get_fields()
{
	return array( 
		'company' => array( 
			'name' => esc_html__( 'Partner Company Slug', 'swt-sawyer' ), 
			'max' => '85', 
			'placeholder' => esc_html__( 'companyslug', 'swt-sawyer' ),
		),
	);
}

// SUBMENU CREATION FUNCTIONS BEGIN HERE /////////////////////////////////////////////////
// Add the submenu to Settings.
function swt_sawyer_add_settings_page()
{
	 add_options_page(
		esc_html__( 'Sawyer for WP', 'swt-sawyer' ),
	 	esc_html__( 'Sawyer for WP', 'swt-sawyer' ),
		'manage_options',
		swt_sawyer_page_slug(),
		'swt_sawyer_render_settings_page'
	 );
}
add_action( 'admin_menu', 'swt_sawyer_add_settings_page' );

// Output the framework of the submenu page.
function swt_sawyer_render_settings_page() 
{
?>
	<div class="wrap">
        <h2><?php esc_html_e( 'Sawyer for WP', 'swt-sawyer' ); ?></h2>
        <form action="options.php" method="post">
            <?php settings_fields( swt_sawyer_option_group() ); ?>
            <?php do_settings_sections( swt_sawyer_page_slug() ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// SETTINGS PAGE CONTENT AND SETTINGS FUNCTIONS BEGIN HERE ///////////////////////////////
// Register the settings, designate the settings section and set up the fields.
function swt_sawyer_settings_init()
{
	$page_slug = swt_sawyer_page_slug();
	$option_group = swt_sawyer_option_group();
	$option_name = swt_sawyer_option_name();
	
	// Register the settings
	register_setting( 
		$option_group, 
		$option_name,
		array(
			'type' => 'string',
			'sanitize_callback' => 'swt_sawyer_validate_settings',
			'show_in_rest' => false,
			'default' => ''
		)
	);
	
	// Set up the settings section
	add_settings_section(
		$option_group, 
		esc_html__( 'Sawyer Setup', 'swt-sawyer' ), 
		'swt_sawyer_render_section',
		$page_slug
	);
	
	// Set up settings fields
	add_settings_field(
		$option_name, 
		esc_html__( 'Settings', 'swt-sawyer' ), 
		'swt_sawyer_render_fields',
		$page_slug, 
		$option_group 
	);
}
add_action( 'admin_init', 'swt_sawyer_settings_init' );

// ADMIN PAGE CONTENT RENDERING FUNCTIONS BEGIN HERE /////////////////////////////////////
// Render the section on the settings page with instructions for setup.
function swt_sawyer_render_section()
{
	echo '<p><strong>';
	esc_html_e( 'The company slug must be set correctly for this plugin to work. Partner companies may obtain the slug name from Sawyer\'s embed code.', 'swt-sawyer' );
	echo "</strong></p>\n<p>";
	esc_html_e( 'The partner company slug appears in the embedded iframe\'s src value. Example:', 'swt-sawyer' );
	echo 'https://www.hisawyer.com/<strong>'.esc_html( 'companyslug', 'swt-sawyer' ).'</strong>/schedules.';
	echo "</p>\n<p>";
	esc_html_e( 'After setup is complete, add the shortcode [sawyer-wp] to the page or pages where you want to display your schedule.', 'swt-sawyer' );
	echo "</p>\n";
}

// Render the settings form fields. Built for expansion of options.
function swt_sawyer_render_fields()
{
	$fields = swt_sawyer_get_fields();
	$option = maybe_unserialize( get_option( swt_sawyer_option_name() ) );
	
	echo '<ul>';
	foreach ( $fields as $key => $field ) {
		
		// Set value; strip embed from key
		$opt = ( isset( $option[$key] ) ? esc_attr( $option[$key] ) : '' );
		
		// Set field defaults if not set
		$max = ( isset( $field['max'] ) && is_numeric( $field['max'] ) ? $field['max'] : 50 );
		$name = ( isset( $field['name'] ) ? $field['name'] : '' );
		$placeholder = ( isset( $field['placeholder'] ) ? $field['placeholder'] : '' );
		
		// Output the field
		echo "<li><label>$name<br />";
		echo '<input type="text" id="swt_sawyer_options_'.$key.'" name="swt_sawyer_options['.$key.']" maxlength="'.$max.'" placeholder="'.$placeholder.'" size="65" value="'.$opt.'" />';
		echo '</label></li>';
	}
	echo '</ul>';
}

// OPTION VALIDATION FUNCTION BEGINS HERE ////////////////////////////////////////////////
// Validation function. Built for expansion of options.
function swt_sawyer_validate_settings( $input )
{
	$fields = swt_sawyer_get_fields();
	$sanitized = array();
	
	foreach ( $fields as $key => $field ) {
	
		if ( isset( $input[$key] ) ) {
		
			// Generic sanitize
			$sanitized[$key] = strtolower( sanitize_text_field( trim( $input[$key] ) ) );
			
			// Sanitized value is empty string
			if ( '' === $sanitized[$key] ) {

        		// But input was not; create error message for bad input
        		$optname = $field['name'];
				add_settings_error(
					"swt_sawyer_option_$key",
					esc_attr( 'settings_updated' ),
					sprintf( esc_html__( 'The value for the %s setting is either invalid or missing. Please check and update it.', 'swt-sawyer' ), $optname ),
					'error'
        		);
				unset( $sanitized[$key] );
    		} // End check for missing value
		
		} // End check for key
	} // Next
	
    return $sanitized;
}