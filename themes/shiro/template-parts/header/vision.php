<?php
/**
 * Setup Vision module
 *
 * @package shiro
 */

$visions = [
	get_theme_mod( 'wmf_vision_lang1' ),
	get_theme_mod( 'wmf_vision_lang2' ),
	get_theme_mod( 'wmf_vision_lang3' ),
	get_theme_mod( 'wmf_vision_lang4' ),
	get_theme_mod( 'wmf_vision_lang5' ),
];
$visions = array_filter( $visions );

$visions_class = [
	get_theme_mod( 'wmf_vision_lang1_class' ),
	get_theme_mod( 'wmf_vision_lang2_class' ),
	get_theme_mod( 'wmf_vision_lang3_class' ),
	get_theme_mod( 'wmf_vision_lang4_class' ),
	get_theme_mod( 'wmf_vision_lang5_class' ),
];

$visions_langcode = [
	get_theme_mod( 'wmf_vision_lang1_langcode' ),
	get_theme_mod( 'wmf_vision_lang2_langcode' ),
	get_theme_mod( 'wmf_vision_lang3_langcode' ),
	get_theme_mod( 'wmf_vision_lang4_langcode' ),
	get_theme_mod( 'wmf_vision_lang5_langcode' ),
];

if ( empty( $visions ) ) {
	$visions[]          = '<span>Imagine a world</span> in which every single human being can freely share in the sum of all knowledge.';
	$visions_class[]    = '';
	$visions_langcode[] = 'en_US';
}

foreach ( $visions as $key => $vision ) {
	$vision_output = [
		'text'        => $visions[ $key ],
		'langcode'    => $visions_langcode[ $key ],
		'aria_hidden' => get_locale() !== $visions_langcode[ $key ],
		'classes'     => [],
	];

	$vision_output['classes'] = [
		0 === $key ? 'is_visible' : '',
		'vision',
		$visions_class[ $key ],
	];
	?>
	<h1 aria-hidden="<?php echo esc_attr( (bool) $vision_output['aria_hidden'] ? 'true' : 'false' ); ?>" lang="<?php echo esc_attr( substr( $vision_output['langcode'], 0, 2 ) ); ?>" class="<?php echo implode( ' ', array_map( 'esc_attr', array_filter( $vision_output['classes'] ) ) ); ?>">
		<?php echo esc_html( $vision_output['text'] ); ?>
	</h1>
	<?php
}
