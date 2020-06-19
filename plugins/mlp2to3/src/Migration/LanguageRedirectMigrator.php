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
 * Migrates a single MLP2 language redirect to MLP3.
 *
 * @package MultilingualPress2to3
 */
class LanguageRedirectMigrator
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
     * Migrates an MLP2 language redirect to MLP3.
     *
     * @param object $mlp2Redirect Data of an MLP2 language redirect. 2 properties required:
     * - `user_id` - ID of the user for which to redirect the language.
     * - `is_redirect` - Whether or not the redirection is enabled, 1 or 0 respectively.
     *
     * @throws Throwable If problem migrating.
     */
    public function migrate($mlp2Redirect)
    {
        $optionName = 'multilingualpress_redirect';
        $userId = intval($mlp2Redirect->user_id);
        $isRedirect = intval($mlp2Redirect->is_redirect);
        $this->_setUserMeta($userId, $optionName, $isRedirect);
    }

    public function _setUserMeta(int $userId, string $key, $value)
    {
        update_user_meta($userId, $key, $value);
        $newValue = intval(get_user_meta($userId, $key, true));

        if ($newValue !== $value) {
            throw new UnexpectedValueException(
                $this->__('Could not update meta key "%1$s" for user "%2$s"', [$key, $userId])
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
