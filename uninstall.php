<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

function swt_sawyer_switch_and_delete( $blog, $opt )
{
	switch_to_blog( $blog );
	delete_option( $opt );
	restore_current_blog();
	return;
}

function swt_sawyer_uninstall_plugin()
{
	$opt = 'swt_sawyer_options';

	if ( !is_multisite() ) {
	
		delete_option( $opt );
		return;

	} else {

		$offset = 0;
		while ( $offset > -1 ) {

			$blogs = get_sites( array( 'fields' => 'ids', 'offset' => $offset ) );

			if ( !$blogs ) {

				return;

			} else {

				foreach ( $blogs as $blog ) {

					swt_sawyer_switch_and_delete( $blog, $opt );

				}
				$offset = $offset + count( $blogs );
			}
		} // Do loop
	} // End check for multisite
}
swt_sawyer_uninstall_plugin();