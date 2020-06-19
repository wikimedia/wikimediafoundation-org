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
use WP_CLI;
use wpdb as Wpdb;

/**
 * Migrates a single MLP2 translatable post type setting to MLP3.
 *
 * @package MultilingualPress2to3
 */
class TranslatablePostTypesMigrator
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
    )
    {

        $this->db = $wpdb;
        $this->translator = $translator;
    }

    /**
     * Migrates an MLP2 translatable post type setting to MLP3.
     *
     * @param object $postType Data of an MLP2 translatable post type setting.
     * - `name` - The name of the post type, i.e. its code.
     * - `is_active` - true or false, depending on whether the type should be translated.
     * - `is_permalink` - true or false, indicating whether dynamic permalinks should be used.
     *
     * @throws Throwable If problem migrating.
     */
    public function migrate($postType)
    {
        $optionName = 'multilingualpress_post_types';
        $typeName = $postType->name;
        $typeIsActive = $postType->is_active;
        $typeIsPermalink = $postType->is_permalink;

        $typesSettings = $this->_getNetworkOption($optionName, []);

        // If already exists and same values, nothing to migrate
        if (array_key_exists($typeName, $typesSettings)
            && ($typesSettings[$typeName]['active'] ?? null) === $typeIsActive
            && ($typesSettings[$typeName]['permalink'] ?? null) === $typeIsPermalink
        ) {
            return;
        }

        $typesSettings[$typeName] = [
            'active'        => (bool) $typeIsActive,
            'permalink'     => (bool) $typeIsPermalink,
        ];

        $this->_setNetworkOption($optionName, $typesSettings);
    }

    /**
     * Retrieves the value of a network option with the specified name.
     *
     * @param string $optionName The name of the option.
     * @param mixed $default The value to retrieve if the option does not exist.
     * @return mixed The value of the option.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getNetworkOption(string $optionName, $default)
    {
        return get_site_option($optionName, $default);
    }

    /**
     * Assigns a value to a network option with the specified name.
     *
     * @param string $optionName Name of the option to set.
     * @param mixed $value The option value.
     *
     * @throws UnexpectedValueException If option could not be set.
     */
    protected function _setNetworkOption(string $optionName, $value)
    {
        $result = update_site_option($optionName, $value);

        if (!$result) {
            throw new UnexpectedValueException($this->__('Could not update network option "%1$s"', [$optionName]));
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
