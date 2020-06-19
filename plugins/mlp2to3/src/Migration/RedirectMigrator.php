<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3\Migration;

use Dhii\I18n\FormatTranslatorInterface;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\I18n\StringTranslatorAwareTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Event\WpTriggerCapableTrait;
use Throwable;
use UnexpectedValueException;
use wpdb as Wpdb;

/**
 * Migrates a single MLP2 redirect to MLP3.
 *
 * @package MultilingualPress2to3
 */
class RedirectMigrator
{
    use WpTriggerCapableTrait;

    use DatabaseWpdbTrait;

    use StringTranslatingTrait;

    use StringTranslatorAwareTrait;

    protected $db;
    protected $translator;

    /**
     * @param Wpdb $wpdb The database driver to use for DB operations.
     * @param FormatTranslatorInterface $translator The translator to use for i18n.
     */
    public function __construct(
        Wpdb $wpdb,
        FormatTranslatorInterface $translator
    ) {

        $this->db = $wpdb;
        $this->translator = $translator;
    }

    /**
     * Migrates an MLP2 redirect to MLP3.
     *
     * @param object $mlp2Redirect Data of an MLP2 redirect. 2 properties required:
     * - `inpsyde_multilingual_redirect` - Value of the redirect. 1 or 0 for true or false, respectively.
     * - `site_id` - The ID of the site, for which this is the redirect value.
     *
     * @throws Throwable If problem migrating.
     */
    public function migrate($mlp2Redirect)
    {
        $optionName = 'multilingualpress_redirect';
        $siteId = (int) $mlp2Redirect->site_id;
        $value = $mlp2Redirect->inpsyde_multilingual_redirect;
        $this->_updateBlogOption($siteId, $optionName, $value);
    }

    /**
     * Updates an option for a particular blog.
     *
     * @param int $siteId The ID of the blog to update the option for.
     * @param string $optionName The name of the option to update.
     * @param mixed $value The value to update the option to.
     *
     * @throws UnexpectedValueException If option value could not be written.
     * @throws Throwable If problem updating.
     */
    protected function _updateBlogOption(int $siteId, string $optionName, $value)
    {
        update_blog_option($siteId, $optionName, $value);
        $newValue = get_blog_option($siteId, $optionName);

        if (!($newValue === $value)) {
            throw new UnexpectedValueException(
                $this->__(
                    'Blog option "%1$s" in site "%2$s" could not be updated to value "%3$s"',
                    [$optionName, $siteId, $value]
                )
            );
        }
    }

    /**
     * Retrieves a table name for a key.
     *
     * @param string $key The key to get the table name for.
     * @return string The table name.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getTableName($key)
    {
        return $this->_getPrefixedTableName($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getDb()
    {
        return $this->db;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getTranslator()
    {
        return $this->translator;
    }
}
