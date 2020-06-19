<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3\Migration;

use Dhii\Data\Container\WritableContainerInterface;
use Dhii\I18n\FormatTranslatorInterface;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\I18n\StringTranslatorAwareTrait;
use Exception;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Event\WpTriggerCapableTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;
use UnexpectedValueException;
use WP_CLI;
use wpdb as Wpdb;

/**
 * Migrates a single MLP2 site language to MLP3.
 *
 * @package MultilingualPress2to3
 */
class SiteLanguageMigrator
{
    use WpTriggerCapableTrait;

    use DatabaseWpdbTrait;

    use StringTranslatingTrait;

    use StringTranslatorAwareTrait;

    protected $db;
    protected $translator;

    /**
     * @var string
     */
    protected $siteSettingsOptionName;
    /**
     * @var ContainerInterface
     */
    protected $optionsContainer;
    /**
     * @var WritableContainerInterface
     */
    protected $meta;

    /**
     * @param Wpdb $wpdb The database driver to use for DB operations.
     * @param FormatTranslatorInterface $translator The translator to use for i18n.
     */
    public function __construct(
        Wpdb $wpdb,
        FormatTranslatorInterface $translator,
        ContainerInterface $optionsContainer,
        WritableContainerInterface $meta,
        string $siteSettingsOptionName
    )
    {
        $this->db = $wpdb;
        $this->translator = $translator;
        $this->optionsContainer = $optionsContainer;
        $this->meta = $meta;
        $this->siteSettingsOptionName = $siteSettingsOptionName;
    }

    /**
     * Migrates an MLP2 site language to MLP3.
     *
     * @param object $mlp2Language Data of an MLP2 site language. Properties
     * - `site_id` - If of the site to which the language belongs.
     * - `locale` - Code of the language's locale.
     * - `title` - The human-readable name of the language.
     *
     * @throws Throwable If problem migrating.
     */
    public function migrate($mlp2Language)
    {
        $optionNameAltTitle = 'multilingualpress_alt_language_title';

        $siteId = $mlp2Language->site_id ? (int) $mlp2Language->site_id : null;
        $locale = $mlp2Language->locale ? (string) $mlp2Language->locale : null;
        $altTitle = $mlp2Language->title ? (string) $mlp2Language->title : '';

        if (empty($siteId)) {
            throw new UnexpectedValueException($this->__('Site ID is required'));
        }

        if (empty($locale)) {
            throw new UnexpectedValueException($this->__('Locale is required for site #%1$d language', [$siteId]));
        }

        try {
            $siteSettings = $this->_getSiteSettings($siteId);
        } catch (UnexpectedValueException $e) {
            $siteSettings = [];
        }

        $siteSettings['lang'] = $this->_transformLanguageCode($locale);
        $this->_setSiteSettings($siteId, $siteSettings);
        $this->_setBlogOption($siteId, $optionNameAltTitle, $altTitle);
    }

    /**
     * Retrieves a meta value of the target site.
     *
     * @param string $optionName The meta key to retrieve.
     * @return mixed The meta value.
     *
     * @throws NotFoundExceptionInterface If option not found.
     * @throws ContainerExceptionInterface If option could not be retrieved.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getSiteMeta(string $optionName)
    {
        $value = $this->meta->get($optionName);

        return $value;
    }

    /**
     * Assigns a meta value to the site.
     *
     * @param string $optionName Name of the option to set.
     * @param mixed $value The option value.
     *
     * @throws ContainerExceptionInterface If option could not be set.
     */
    protected function _setSiteMeta(string $optionName, $value)
    {
        $this->meta->set($optionName, $value);
    }

    /**
     * Retrieves settings for a site.
     *
     * @return array<string, mixed> A map of site info keys to values.
     *
     * @throws UnexpectedValueException If no settings found for specified site.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getSiteSettings(int $siteId): array
    {
        $allSettings = $this->_getAllSiteSettings();

        if (!array_key_exists($siteId, $allSettings)) {
            throw new UnexpectedValueException($this->__('No settings for site #%1$d', [$siteId]));
        }

        $siteSettings = $allSettings[$siteId];

        return $siteSettings;
    }

    /**
     * Assigns settings for a site.
     *
     * @param int $siteId
     * @param $value
     *
     * @throws Exception If problem assigning.
     * @throws Throwable If problem running.
     */
    protected function _setSiteSettings(int $siteId, $value)
    {
        try {
            $allSettings = $this->_getAllSiteSettings();
        } catch (NotFoundExceptionInterface $e) {
            $allSettings = [];
        }

        $allSettings[$siteId] = $value;

        $this->_setAllSiteSettings($allSettings);
    }

    /**
     * Assigns the settings for all sites.
     *
     * @param array<int, array<string, mixed>> $settings A map of site ID to site settings.
     *
     * @throws ContainerExceptionInterface If could not set settings.
     * @throws Exception If problem setting.
     * @throws Throwable If problem running.
     */
    protected function _setAllSiteSettings(array $settings)
    {
        $optionName = $this->siteSettingsOptionName;

        $this->_setSiteMeta($optionName, $settings);
    }

    /**
     * Retrieves the settings for all sites.
     *
     * @return array<int, array<string, mixed>> $settings A map of site ID to site settings.
     *
     * @throws NotFoundExceptionInterface If settings not found.
     * @throws ContainerExceptionInterface If settings value could not be retrieved.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getAllSiteSettings(): array
    {
        $optionName = $this->siteSettingsOptionName;

        try {
            $siteSettings = $this->_getSiteMeta($optionName);
        } catch (NotFoundExceptionInterface $e) {
            $siteSettings = [];
        }

        return $siteSettings;
    }

    /**
     * Assigns a value to an option of a blog.
     *
     * @param int $blogId ID of the blog to set the option for.
     * @param string $optionName The name of the option to set.
     * @param mixed $value The option value.
     *
     * @throws ContainerExceptionInterface If could not set.
     */
    public function _setBlogOption(int $blogId, string $optionName, $value)
    {
        $this->optionsContainer->get($blogId)->set($optionName, $value);
    }

    /**
     * Transforms a source language code into a destination language code.
     *
     * @param string $code The code to transform.
     *
     * @return string The transformed code.
     * Usually in BCP47 format.
     */
    protected function _transformLanguageCode(string $code): string
    {
        $code = str_replace('_', '-', $code);

        return $code;
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
