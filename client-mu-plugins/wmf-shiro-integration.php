<?php
/**
 * Integrate the Shiro theme with other plugins and site-specific functionality.
 *
 * @package wmf-shiro-integration
 */


add_filter(
    'wmf_shiro_allowed_blocks',
    /**
     * Filter the blocks allowed to render in the Shiro theme.
     *
     * @param string[] $blocks Array of block type slugs which should be allowed.
     * @return string[] filtered block types.
     */
    function( array $blocks ) : array {
        // Supported third-party blocks
        $blocks[] = 'vegalite-plugin/visualization';
        $blocks[] = 'vegalite-plugin/responsive-container';
        $blocks[] = 'simple-editorial-comments/editorial-comment';
        $blocks[] = 'simple-editorial-comments/hidden-group';
        return $blocks;
    }
);
