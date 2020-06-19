<?php

namespace Inpsyde\MultilingualPress2to3;

use cli\Progress;
use Dhii\I18n\StringTranslatorAwareTrait;
use Dhii\I18n\StringTranslatorConsumingTrait;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Inpsyde\MultilingualPress2to3\Migration\LanguageMigrator;
use Inpsyde\MultilingualPress2to3\Migration\RedirectMigrator;
use Inpsyde\MultilingualPress2to3\Migration\SiteLanguageMigrator;
use League\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use Throwable;
use UnexpectedValueException;
use WP_Site;
use wpdb as Wpdb;

/**
 * Migrates a series of site language records from MLP2 to MLP3 format.
 *
 * @package MultilingualPress2to3
 */
class SiteLanguagesMigrationHandler implements HandlerInterface
{
    use DatabaseWpdbTrait;

    use StringTranslatorConsumingTrait;
    use StringTranslatorAwareTrait;

    /**
     * @var SiteLanguageMigrator
     */
    protected $migrator;
    /**
     * @var Progress
     */
    protected $progress;
    /**
     * @var Wpdb
     */
    protected $db;
    /**
     * @var int
     */
    protected $limit;
    /**
     * @var int
     */
    protected $mainSiteId;
    /**
     * @var string
     */
    protected $siteLanguagesOptionName;

    /**
     * @param SiteLanguageMigrator $migrator The migrator that migrates site languages.
     * @param Wpdb $db The DB adapter.
     * @param Progress $progress The progress that tracks migration... progress.
     * @param int $limit How many records to migrate. 0 means no limit.
     */
    public function __construct(
        SiteLanguageMigrator $migrator,
        Wpdb $db,
        Progress $progress,
        int $limit,
        int $mainSiteId,
        string $siteLanguagesOptionName
    ) {
        $this->migrator = $migrator;
        $this->progress = $progress;
        $this->db = $db;
        $this->limit = $limit;
        $this->mainSiteId = $mainSiteId;
        $this->siteLanguagesOptionName = $siteLanguagesOptionName;
    }

    /**
     * Migrates a number of languages.
     *
     * @throws Throwable If problem running.
     */
    public function run()
    {
        $langs = $this->_getLanguagesToMigrate();
        $count = count ($langs);
        $progress = $this->_getProgress($count);

        foreach ($langs as $redirect) {
            $this->_getMigrator()->migrate($redirect);
            $progress->tick();
        }
//
        $progress->finish();
    }

    /**
     * Retrieves MLP2 site languages to migrate to MLP3.
     *
     * @return object[] A list of objects, each representing an MLP2 site language.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getLanguagesToMigrate()
    {
        $sourceLangs = $this->_getSourceLanguages();
        $result = [];

        foreach ($sourceLangs as $siteId => $info) {
            $lang = (object) [
                'site_id'           => $siteId,
                'locale'            => $info['lang'] ?? '',
                'title'             => $info['text'] ?? '',
            ];
            $result[] = $lang;
        }

        return $result;
    }

    /**
     * Retrieves the site languages to migrate.
     *
     * @return object[] A list of objects, each representing a source site language.
     *
     * @throws Throwable If problem running.
     */
    protected function _getSourceLanguages()
    {
        $networkId = $this->mainSiteId;
        $optionName = $this->siteLanguagesOptionName;
        $languages = $this->_getNetworkOption($networkId, $optionName);
        assert(is_array($languages));

        return $languages;
    }

    /**
     * Retrieves an option of a particular network.
     *
     * @param int $networkId The ID of the network, to which the option belongs.
     * @param string $optionName The name of the option to retrieve.
     * @return mixed The option value.
     *
     * @throws UnexpectedValueException If option could not be retrieved.
     */
    protected function _getNetworkOption(int $networkId, string $optionName)
    {
        $default = uniqid('default-network-option-value-');
        $value = get_network_option($networkId, $optionName, $default);

        if ($value === $default) {
            throw new UnexpectedValueException($this->__('Could not retrieve option "%1$s" for network #%2$d', [$optionName, $networkId]));
        }

        return $value;
    }

    /**
     * Retrieves the database driver associated with this instance.
     *
     * @return Wpdb The database driver.
     *
     * @throws Throwable If problem retrieving driver.
     */
    protected function _getDb()
    {
        return $this->db;
    }

    /**
     * Retrieves the migrator that migrates a single site language.
     *
     * @return SiteLanguageMigrator A list of handlers.
     */
    protected function _getMigrator()
    {
        return $this->migrator;
    }

    /**
     * Retrieves the progress element associated with this instance.
     *
     * @return Progress The progress.
     */
    protected function _getProgress($count = null): Progress
    {
        $progress = $this->progress;

        if (is_int($count)) {
            $progress->reset($count);
        }

        return $progress;
    }

    /**
     * Retrieves the maximal amount of records to migrate.
     *
     * @return int The limit.
     *
     * @throws Throwable If problem running.
     */
    protected function _getLimit(): int
    {
        return $this->limit;
    }
}