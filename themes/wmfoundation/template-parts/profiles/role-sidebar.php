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
<ul class="resource-list">
<?php foreach ( $post_list as $term_id => $post_data ) :
	wmf_get_template_part( 'template-parts/profiles/role-item-link', array(
		'term_id' => $term_id,
		'name'    => $post_data['name']
	) );

	foreach ( $post_data['children'] as $term_id => $post_data ) :
		 wmf_get_template_part( 'template-parts/profiles/role-item-link', array(
			'term_id' => $term_id,
			'name'    => $post_data['name']
		) );
	endforeach;
endforeach;
?>
</ul>
