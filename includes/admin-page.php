<?php
namespace MIGHTYminnow\DashboardNotes;

/**
 * Enqueue Scripts
 */
add_action( 'admin_enqueue_scripts', function( $hook_suffix ) {
	if ( 'toplevel_page_dashboard-notes' != $hook_suffix ) {
		return;
	}

	wp_enqueue_script(
		'dashboard-notes-admin',
		DASHBOARD_NOTES_URL . 'js/dashboard-notes-admin.js',
		array( 'jquery' ),
		DASHBOARD_NOTES_VERSION,
		true
	);
} );

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
	global $dashboard_notes;
	?>
	<div class="wrap">
		<h1>Dashboard Notes</h1>
		<h2>Add New Note</h2>
		<form id="dn-add" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
			<div>
				<label for="dn-add-title"><?php _e( 'Title', 'dashboard-notes' ); ?></label>
				<input type="text" id="dn-add-title" name="title" required>
			</div>

			<div>
				<label for="dn-add-content"><?php _e( 'Content', 'dashboard-notes' ); ?></label>
				<textarea id="dn-add-content" name="content" rows="6" required></textarea>
			</div>

			<input type="hidden" name="action" value="save_dashboard_note">
			<?php wp_nonce_field( 'save_dashboard_note_nonce', 'save_dashboard_note_nonce_field' ); ?>

			<div>
				<button type="submit" id="dn-add-submit">Save</button>
			</div>
		</form>

		<h2>Current Notes</h2>
		<?php
		$widgets = array(); // Widgets

		// For each widget in the dashboard notes sidebar
		$sidebars_widgets = get_option( 'sidebars_widgets' );
		foreach ( $sidebars_widgets['dashboard-notes'] as $widget_id ) {
			preg_match( '/^(.*)-(\d+)$/', $widget_id, $matches );
			$widget_name = $matches[1];
			$widget_index = $matches[2];

			// Get all widgets of the type
			if ( ! isset( $widgets[ $widget_name ] ) ) {
				$widgets[ $widget_name ] = get_option( "widget_{$widget_name}" );
			}

			$sidebars_widgets = get_option( 'sidebars_widgets' );

			foreach ( $sidebars_widgets['dashboard-notes'] as $widget_id ) {
				preg_match( '/^(.*)-(\d+)$/', $widget_id, $widget );
				$widget_type = $widget[1] ?? '';
				$widget_index = $widget[2] ?? '';

				$widget_title = $widgets[ $widget_type ][ $widget_index ]['title'] ?? '';
				$widget_content = $widgets[ $widget_type ][ $widget_index ]['content'] ?? '';
				?>
				<div>
					<h2>Edit Note</h2>
					<form id="dn-edit-<?php echo $widget_id; ?>" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
						<div>
							<label for="dn-edit-title-<?php echo $widget_id; ?>"><?php _e( 'Title', 'dashboard-notes' ); ?></label>
							<input type="text" id="dn-edit-title-<?php echo $widget_id; ?>" name="title" required value="<?php echo $widget_title; ?>">
						</div>

						<div>
							<label for="dn-edit-content"><?php _e( 'Content', 'dashboard-notes' ); ?></label>
							<textarea id="dn-edit-content" name="content" rows="6" required><?php echo $widget_content; ?></textarea>
						</div>

						<input type="hidden" name="action" value="edit_dashboard_note">
						<input type="hidden" name="note_id" value="<?php echo $widget_id; ?>">
						<?php wp_nonce_field( "edit_dashboard_note_{$widget_id}", 'edit_dashboard_note_nonce' ); ?>

						<div>
							<button type="submit" id="dn-edit-submit-<?php echo $widget_id; ?>">Save</button>
						</div>
					</form>
				</div>
				<?php
			}

			// // Print Widget Data
			// echo '<pre>';
			// print_r( $widgets[ $widget_name ][ $widget_index ] );
			// echo '</pre>';

			// // Print Widget Dashboard Notes Options
			// echo '<pre>';
			// print_r( $dashboard_notes->dn_options[ $widget_id ] );
			// echo '</pre>';
		}
		?>
	</div>
	<?php
}

add_action( 'admin_post_save_dashboard_note', __NAMESPACE__ . '\save_note' );

function save_note() {
	check_admin_referer( 'save_dashboard_note_nonce', 'save_dashboard_note_nonce_field' );

	$title = sanitize_text_field( $_POST['title'] );
	$content = sanitize_textarea_field( $_POST['content'] );

	$widget_type = 'custom_html'; // ID base of the widget (e.g. 'text', 'recent-posts', 'nav_menu', etc.)
	$sidebar_id  = 'dashboard-notes'; // Sidebar ID (as registered in your code)
	
	// 1. Get all existing widget instances of that type
	$all_widgets = get_option( 'widget_' . $widget_type, [] );

	// 2. Create a new instance
	$new_widget = [
		'title' => $title,
		'content' => $content,
	];

	// 3. Append to the array and get the new instance key
	$all_widgets[] = $new_widget;
	end( $all_widgets );
	$new_instance_id = key( $all_widgets );

	// 4. Save back the updated widget options
	update_option( 'widget_' . $widget_type, $all_widgets );

	// 5. Assign that widget instance to a sidebar
	$sidebars_widgets = get_option( 'sidebars_widgets', [] );

	// Make sure the sidebar exists
	if ( ! isset( $sidebars_widgets[ $sidebar_id ] ) ) {
		$sidebars_widgets[ $sidebar_id ] = [];
	}

	$sidebars_widgets[ $sidebar_id ][] = $widget_type . '-' . $new_instance_id;

	update_option( 'sidebars_widgets', $sidebars_widgets );

	// 6. Add Dashboard Notes options
	$options = get_option( 'dashboard_notes_options' );

	$options[ $widget_type . '-' . $new_instance_id ] = array(
		'style' => 'red',
		'include-logo' => 1,
		'logo-url' => '',
		'incexc' => 'show',
		'url' => array(
			'urls' => '',
		),
	);

	update_option( 'dashboard_notes_options', $options );

	wp_redirect( admin_url( 'admin.php?page=dashboard-notes&saved=1' ) );
	exit;
}

add_action( 'admin_post_edit_dashboard_note', __NAMESPACE__ . '\edit_note' );

function edit_note() {
	check_admin_referer( 'edit_dashboard_note_' . $_POST['note_id'], 'edit_dashboard_note_nonce' );

	$widget_data = parse_widget_id( $_POST['note_id'] );

	$widget_instances = get_option( 'widget_' . $widget_data['type'], array() );

	$widget_instances[ $widget_data['index'] ] = array(
		'title' => $_POST['title'],
		'content' => $_POST['content'],
	);

	update_option( 'widget_' . $widget_data['type'], $widget_instances );

	wp_redirect( admin_url( 'admin.php?page=dashboard-notes&updated=1' ) );
	exit;
}

function parse_widget_id( $widget_id ) {
	preg_match( '/^(.*)-(\d+)$/', $widget_id, $matches );

	return array(
		'id' => $widget_id,
		'type'  => isset( $matches[1] ) ? $matches[1] : '',
		'index' => isset( $matches[2] ) ? (int) $matches[2] : null,
	);
}

add_action( 'admin_init', function() {
	if ( isset( $_GET['debug'] ) ) {
		if ( ! function_exists( 'next_widget_id_number' ) ) {
			require_once ABSPATH . 'wp-admin/includes/widgets.php';
		}
		echo \next_widget_id_number( 'custom_html' );
		echo '<pre>';
		print_r( get_option( 'widget_custom_html' ) );
		
		exit;
	}

}, 9999999 );