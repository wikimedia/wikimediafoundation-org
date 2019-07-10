<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 * Adds media content output
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

echo '<?xml version="1.0" encoding="'.esc_attr(get_option('blog_charset')).'"?'.'>';

/**
 * Fires between the <xml> and <rss> tags in a feed.
 *
 * @since 4.0.0
 *
 * @param string $context Type of feed. Possible values include 'rss2', 'rss2-comments',
 *                        'rdf', 'atom', and 'atom-comments'.
 */
do_action( 'rss_tag_pre', 'rss2' );
?><rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    xmlns:media="http://search.yahoo.com/mrss/"
	<?php do_action('rss2_ns'); ?>
>
<channel>
	<title><?php wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo esc_html(mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false)); ?></lastBuildDate>
	<?php the_generator( 'rss2' ); ?>
	<language><?php echo esc_html(get_option('rss_language')); ?></language>
	<sy:updatePeriod><?php echo esc_html(apply_filters( 'rss_update_period', 'hourly' )); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo esc_html(apply_filters( 'rss_update_frequency', '1' )); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	<?php while( have_posts()) : the_post(); ?>

	<item>
		<title><?php the_title_rss(); ?></title>
		<link><?php the_permalink_rss(); ?></link>
		<comments><?php comments_link(); ?></comments>
		<pubDate><?php echo esc_html(mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false)); ?></pubDate>
		<dc:creator><?php the_author(); ?></dc:creator>
<?php the_category_rss(); ?>

<?php if (has_post_thumbnail( $post->ID ) ): ?>
  <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
<?php endif; ?>
	<media:content url="<?php echo esc_url($image[0]); ?>" width="500" height="300" medium="image" />
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php if (get_option('rss_use_excerpt')) : ?>

		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
<?php else : ?>

		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
	<?php if ( strlen( $post->post_content ) > 0 ) : ?>

		<content:encoded><![CDATA[<?php the_content() ?>]]></content:encoded>
	<?php else : ?>

		<content:encoded><![CDATA[<?php the_excerpt_rss() ?>]]></content:encoded>
	<?php endif; ?>
<?php endif; ?>

		<wfw:commentRss><?php echo esc_html(get_post_comments_feed_link()); ?></wfw:commentRss>
		<slash:comments><?php echo esc_html(get_comments_number()); ?></slash:comments>
<?php rss_enclosure(); ?>
<?php do_action('rss2_item'); ?>

	</item>
	<?php endwhile; ?>

</channel>
</rss>