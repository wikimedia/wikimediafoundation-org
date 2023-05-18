<?php
/**
 * Breadcrumb Links
 */

namespace WMF\Breadcrumb_Links;

/**
 * Kick it off.
 */
function init() {
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\\add_breadcrumb_link_meta_box' );
	add_action( 'save_post', __NAMESPACE__ . '\\save_breadcrumb_link_custom_fields' );
}


/**
 * Add the breadcrumb_link meta box.
 */
function add_breadcrumb_link_meta_box() {
	add_meta_box(
		'breadcrumb_link_meta_box',
		__( 'Breadcrumb Link Setup', 'shiro-admin' ),
		__NAMESPACE__ . '\\display_breadcrumb_link_meta_box',
		'page',
		'side',
	);
}

/**
 * Display the breadcrumb link meta box.
 *
 * @param WP_Post $post The post object.
 *
 * @return void
 */
function display_breadcrumb_link_meta_box( $post ) : void {
	$show_breadcrumb_links = get_post_meta( $post->ID, 'show_breadcrumb_links', true );
	$breadcrumb_link_url = get_post_meta( $post->ID, 'breadcrumb_link_url', true );
	$breadcrumb_link_title = get_post_meta( $post->ID, 'breadcrumb_link_title', true );
	?>

	<?php wp_nonce_field( basename( __FILE__ ), 'breadcrumb_link_nonce' ); ?>

	<label for="show_breadcrumb_links">
		<input type="checkbox" id="show_breadcrumb_links" name="show_breadcrumb_links" <?php checked( $show_breadcrumb_links, 'on' ); ?> />
		<?php esc_html_e( 'Show Breadcrumb Link', 'shiro-admin' ); ?>
	</label><br /><br />

	<label for="breadcrumb_link_url">
		<?php esc_html_e( 'Custom Link', 'shiro-admin' ); ?>
		<input
			type="text"
			id="breadcrumb_link_url"
			name="breadcrumb_link_url"
			value="<?php echo esc_attr( $breadcrumb_link_url ); ?>"
			placeholder="<?php esc_attr_e( 'Leave blank to use default link', 'shiro-admin' ); ?>"
		/>
	</label><br /><br />

	<label for="breadcrumb_link_title">
		<?php esc_html_e( 'Custom Text', 'shiro-admin' ); ?>
		<input
			type="text"
			id="breadcrumb_link_title"
			name="breadcrumb_link_title"
			value="<?php echo esc_attr( $breadcrumb_link_title ); ?>"
			placeholder="<?php esc_attr_e( 'Leave blank to use default title', 'shiro-admin' ); ?>"
		/>
	</label>

	<?php
}

/**
 * Save the breadcrumb_link meta when the post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 */
function save_breadcrumb_link_custom_fields( $post_id ) : void {
	if ( ! isset( $_POST['breadcrumb_link_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['breadcrumb_link_nonce'] ), basename( __FILE__ ) ) ) {
		return;
	}

	if ( isset( $_POST['show_breadcrumb_links'] ) ) {
		update_post_meta( $post_id, 'show_breadcrumb_links', 'on' );
	} else {
		update_post_meta( $post_id, 'show_breadcrumb_links', 'off' );
	}

	if ( isset( $_POST['breadcrumb_link_url'] ) ) {
		update_post_meta( $post_id, 'breadcrumb_link_url', sanitize_text_field( $_POST['breadcrumb_link_url'] ) );
	}

	if ( isset( $_POST['breadcrumb_link_title'] ) ) {
		update_post_meta( $post_id, 'breadcrumb_link_title', sanitize_text_field( $_POST['breadcrumb_link_title'] ) );
	}
}
