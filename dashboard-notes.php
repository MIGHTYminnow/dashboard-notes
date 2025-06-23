<?php
/**
 * Dashboard Notes
 *
 * Plugin Name:       Dashboard Notes
 * Plugin URI:        http://wordpress.org/plugins/dashboard-notes
 * Description:       Create dashboard notes/instructions for your client.
 * Version:           2.0.0-alpha
 * Author:            MIGHTYminnow
 * Author URI:        http://mightyminnow.com
 * Text Domain:       dashboard-notes
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dashboard-notes
 * Domain Path:       /languages
 */

namespace MIGHTYminnow\DashboardNotes;

/**
 * Register the admin page.
 */
function register_admin_page() {
	add_menu_page(
		esc_html__( 'Dashboard Notes', 'dashboard-notes' ),
		esc_html__( 'Dashboard Notes', 'dashboard-notes' ),
		'manage_options',
		'dashboard-notes',
		__NAMESPACE__ . '\display_admin_page',
		'dashicons-admin-generic',
		99
	);
}

add_action( 'admin_menu', __NAMESPACE__ . '\register_admin_page' );

/**
 * Display the admin page.
 */
function display_admin_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Dashboard Notes', 'dashboard-notes' ); ?></h1>
	</div>
	<?php
}
