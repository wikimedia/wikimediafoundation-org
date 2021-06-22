<?php
/**
 * List of downloads in default page sidebar
 *
 * @package shiro
 */

$downloads       = $args;
$downloads_title = get_theme_mod( 'wmf_downloads_header', __( 'Downloads', 'shiro-admin' ) );

if ( empty( $downloads ) ) {
	return;
}

?>

<div class="download-container">
	<h3><?php echo esc_html( $downloads_title ); ?></h3>

	<ul class="list-download">
	<?php
	foreach ( $downloads as $download ) :
		$title         = ! empty( $download['title'] ) ? $download['title'] : '';
		$link          = ! empty( $download['link'] ) ? $download['link'] : '';
		$file          = ! empty( $download['file'] ) ? wp_get_attachment_url( $download['file'] ) : '';
		$download_file = empty( $link ) ? $file : $link;

		if ( empty( $title ) || empty( $download_file ) ) {
			continue;
		}
		?>
	<li>
	<a href="<?php echo esc_url( $download_file ); ?>" download>
		<?php wmf_show_icon( 'download', 'material' ); ?>
		<span class="download-content">
			<?php echo esc_html( $title ); ?>
		</span>
	</a>
	</li>
	<?php endforeach; ?>
	</ul>
</div>
