<?php
/**
 * Adds a sidebar to the roles archive page
 *
 * @package wmfoundation
 */

$post_list = wmf_get_template_data();

if ( empty( $post_list ) || count( $post_list ) === 1 ) {
	return;
}

?>
<div class="w-32p display-none_small">
<ul class="toc fixedsticky">
<?php
foreach ( $post_list as $term_id => $post_data ) :
	wmf_get_template_part(
		'template-parts/profiles/role-item-link', array(
			'term_id' => $term_id,
			'name'    => $post_data['name'],
		)
	);
endforeach;
?>
</ul>
</div>
