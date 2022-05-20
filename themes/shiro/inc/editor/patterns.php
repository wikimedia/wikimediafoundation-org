<?php
/**
 * Block patterns for content editors to quickly create content.
 */

namespace WMF\Editor\Patterns;

/**
 * @var string The slug for the block pattern category to group these into.
 */
const MAIN_CATEGORY_NAME = 'wikimedia';

/**
 * @var string The slug for patterns that are meant for migration to populate
 *             based on a template.
 */
const TEMPLATE_CATEGORY_NAME = 'wikimedia-templates';

/**
 * Hook into WordPress
 */
function bootstrap() {
	add_action( 'after_setup_theme', __NAMESPACE__ . '\\register_pattern' );
}

function register_pattern() {
	register_block_pattern_category( MAIN_CATEGORY_NAME, [
		'label' => __( 'Wikimedia', 'shiro-admin' ),
	] );

	register_block_pattern_category( TEMPLATE_CATEGORY_NAME, [
		'label' => __( 'Wikimedia templates', 'shiro-admin' ),
	] );

	register_block_pattern( FactColumns\NAME, [
		'title' => __( 'Numbered fact columns', 'shiro-admin' ),
		'categories' => [ MAIN_CATEGORY_NAME ],
		'content' => FactColumns\PATTERN,
	] );

	register_block_pattern( TweetColumns\NAME, [
		'title' => __( 'Tweet this columns', 'shiro-admin' ),
		'categories' => [ MAIN_CATEGORY_NAME ],
		'content' => TweetColumns\pattern(),
	] );

	register_block_pattern( LinkColumns\NAME, [
		'title' => __( 'External link columns', 'shiro-admin' ),
		'categories' => [ MAIN_CATEGORY_NAME ],
		'content' => LinkColumns\PATTERN,
	] );

	register_block_pattern( CardColumns\NAME, [
		'title' => __( 'Cards', 'shiro-admin' ),
		'categories' => [ MAIN_CATEGORY_NAME ],
		'content' => CardColumns\PATTERN,
	] );

	register_block_pattern( BlogList\NAME, [
		'title' => __( 'Blog list section', 'shiro-admin' ),
		'categories' => [ MAIN_CATEGORY_NAME ],
		'content' => BlogList\PATTERN,
	] );

	register_block_pattern( CommunicationModule\NAME, [
		'title' => __( 'Communication module', 'shiro-admin' ),
		'categories' => [ MAIN_CATEGORY_NAME ],
		'content' => CommunicationModule\PATTERN,
	] );

	register_block_pattern( TemplateDefault\NAME, [
		'title' => __( 'Default Template', 'shiro-admin' ),
		'categories' => [ TEMPLATE_CATEGORY_NAME ],
		'content' => TemplateDefault\pattern(),
	] );

	register_block_pattern( TemplateLanding\NAME, [
		'title' => __( 'Landing page template', 'shiro-admin' ),
		'categories' => [ TEMPLATE_CATEGORY_NAME ],
		'content' => TemplateLanding\pattern(),
	] );

	register_block_pattern( TemplateList\NAME, [
		'title' => __( 'List page template', 'shiro-admin' ),
		'categories' => [ TEMPLATE_CATEGORY_NAME ],
		'content' => TemplateList\pattern(),
	] );

	register_block_pattern( TemplateReportLanding\NAME, [
		'title' => __( 'Report landing page template', 'shiro-admin' ),
		'categories' => [ TEMPLATE_CATEGORY_NAME ],
		'content' => TemplateReportLanding\pattern(),
	] );

	register_block_pattern( TemplateReportSection\NAME, [
		'title' => __( 'Report section page template', 'shiro-admin' ),
		'categories' => [ TEMPLATE_CATEGORY_NAME ],
		'content' => TemplateReportSection\pattern(),
	] );

	register_block_pattern( TemplateReport\NAME, [
		'title' => __( 'Report page template', 'shiro-admin' ),
		'categories' => [ TEMPLATE_CATEGORY_NAME ],
		'content' => TemplateReport\pattern(),
	] );
}
