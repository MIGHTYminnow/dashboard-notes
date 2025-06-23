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

		<div class="dashboard-notes-controls">
			<h3>Dashboard Notes</h3>
			<p>
				<label class="dn-style">
					<b>Style</b><br> 
					<select name="dn[custom_html-2][style]">
						<option value="mm-green">MIGHTYminnow Green</option>
						<option value="green">Green</option>
						<option value="red" selected="selected">Red</option>
						<option value="orange">Orange</option>
						<option value="yellow">Yellow</option>
						<option value="blue">Blue</option>
					</select> 
				</label>
			</p>
			<p>
				<b>Logo</b><br>
				<label class="dn-include-logo">
					<input type="checkbox" value="1" name="dn[custom_html-2][include-logo]" checked="checked">&nbsp;Include logo
				</label>
			</p>
			<p>
				<label class="dn-logo-url widefat">Logo URL:<br>
					<input type="text" class="widefat" name="dn[custom_html-2][logo-url]" value="<?php echo esc_url( plugins_url( '/assets/images/mm-logo.png', __FILE__ ) ); ?>"><br>
					<small><i>(defaults to MIGHTYminnow logo if no URL is specified</i></small>
				</label>
			</p>
			<p>
				<label class="dn-incexc">
					<b>Where to show</b><br> 
					<select name="dn[custom_html-2][incexc]">
						<option value="show">Show everywhere</option><option value="hide">Hide everywhere</option><option value="selected" selected="selected">Show on selected URLs</option><option value="notselected">Hide on selected URLs</option>
					</select>
				</label>
			</p>
			<div class="dn-urls">
				<label>
					<strong>Target URLs</strong>
					<textarea class="widefat" name="dn[custom_html-2][url][urls]">plugins.php</textarea>
				</label>
				<p class="dn-tip">Enter one location fragment per line. Use <strong>*</strong> character as a wildcard. Example: <code>category/peace/*</code> to target all posts in category <em>peace</em>.</p>
			</div>
		</div>
	</div>
	<?php
}
