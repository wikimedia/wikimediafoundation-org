<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3\Migration;

use Dhii\I18n\FormatTranslatorInterface;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\I18n\StringTranslatorAwareTrait;
use DomainException;
use Inpsyde\MultilingualPress2to3\Db\DatabaseWpdbTrait;
use Inpsyde\MultilingualPress2to3\Event\WpTriggerCapableTrait;
use Psr\Container\ContainerInterface;
use Exception;
use Throwable;
use Psr\Container\NotFoundExceptionInterface;
use wpdb as Wpdb;

/**
 * Migrates a single MLP2 language to MLP3.
 *
 * @package MultilingualPress2to3
 */
class LanguageMigrator
{
    use WpTriggerCapableTrait;

    use DatabaseWpdbTrait;

    use StringTranslatingTrait;

    use StringTranslatorAwareTrait;

    /**
     * @var Wpdb
     */
    protected $db;
    /**
     * @var FormatTranslatorInterface
     */
    protected $translator;
    /**
     * @var string
     */
    protected $languagesTableName;
    /**
     * @var ContainerInterface
     */
    protected $languages;
    /**
     * @var ContainerInterface
     */
    protected $locales;

    /**
     * @param Wpdb $wpdb The database driver to use for DB operations.
     * @param FormatTranslatorInterface $translator The translator to use for i18n.
     * @param string $languagesTableName Name of the table to store languages in.
     */
    public function __construct(
        Wpdb $wpdb,
        FormatTranslatorInterface $translator,
        ContainerInterface $locales,
        ContainerInterface $languages,
        string $languagesTableName
    ) {

        $this->db = $wpdb;
        $this->translator = $translator;
        $this->languagesTableName = $languagesTableName;
        $this->languages = $languages;
        $this->locales = $locales;
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
    public function migrate($mlp2Language)
    {
        if ($this->_isEmbeddedLanguage($mlp2Language)) {
            return;
        }

        $mlp3Language = $this->_convertLanguage($mlp2Language);

        $this->_saveLanguage($mlp3Language);
    }

    /**
     * Transforms a source language entry into target format.
     *
     * @param object $source An object that represents a source language entry.
     *
     * @return object An object that represents a target language entry.
     *
     * @throws Throwable If problem converting.
     */
    protected function _convertLanguage($source)
    {
        $mlp3Language = (object) [
            'id' => $source->id,
            'english_name' => $source->english_name,
            'native_name' => $source->native_name,
            'custom_name' => $source->custom_name,
            'is_rtl' => $source->is_rtl,
            'iso_639_1' => $source->iso_639_1,
            'iso_639_2' => $source->iso_639_2,
            'locale' => $source->locale,
            'http_code' => $source->bcp47,
        ];

        return $mlp3Language;
    }

    /**
     * Persists a language entry.
     *
     * @param object $language An object that represents a language entry.
     *
     * @throws Exception If problem saving.
     * @throws Throwable If problem running.
     */
    protected function _saveLanguage($language)
    {
        $tableName = $this->_getTableName($this->languagesTableName);
        $this->_insert($tableName, $language);
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
     * Retrieves a language record matching a specified locale code.
     *
     * @param string $localeCode The BCP47 code of the locale.
     * @return object A map of field names to values, representing a language entry.
     *
     * @throws NotFoundExceptionInterface If locale or its language could not be found.
     * @throws Exception If problem accessing members.
     * @throws Throwable If problem retrieving.
     */
    protected function _getLanguageForSource(string $localeCode)
    {
        $locale = $this->_getLocale($localeCode);

        if (!property_exists($locale, 'language')) {
            throw new DomainException($this->__('Locale "%1$s" must have a "language" member', [$localeCode]));
        }

        $language = $locale->language;

        if (!property_exists($language, 'iso-639-3')) {
            throw new DomainException($this->__('The language of locale "%1$s" must have a "iso-639-3" member', [$localeCode]));
        }

        $languageCode = $language->{'iso-639-3'};
        $language = $this->_getLang($languageCode);
        $newLang = [
            'locale'            => $locale->code ?? '',
            'http_code'         => $locale->bcp47 ?? '',
            'english_name'      => $language->{'english-name'} ?? '',
            'native_name'       => $language->{'native-name'} ?? '',
            'custom_name'       => '',
            'is_rtl'            => $language->rtl ? '1' : '0',
            'iso_639_1'         => $language->{'iso-639-1'} ?? '',
            'iso_639_2'         => $language->{'iso-639-2'} ?? '',
        ];

        return (object) $newLang;
    }

    /**
     * Retrieve a destination language key for a source language key.
     *
     * @param string $sourceLangKey The source language key.
     * @return string The destination language key.
     */
    protected function _getDestinationLangKey(string $sourceLangKey): string
    {
        $map = [
            'bcp47'             => 'http_code',
        ];

        if (array_key_exists($sourceLangKey, $map)) {
            return $map[$sourceLangKey];
        }

        return $sourceLangKey;
    }

    /**
     * Determines whether there is a target entry that corresponds to the given source entry.
     *
     * @param object $sourceLang An object representing a source language entry.
     * @return bool True if a target exists, and all of its fields match the source; false otherwise.
     *
     * @throws Exception If problem determining.
     * @throws Throwable If problem running.
     */
    protected function _isEmbeddedLanguage($sourceLang)
    {
        try {
            $lang = $this->_getLanguageForSource($sourceLang->bcp47);

            foreach ($sourceLang as $key => $value) {
                $destKey = $this->_getDestinationLangKey($key);

                if (!property_exists($lang, $destKey)) {
                    continue;
                }

                if ($lang->{$destKey} !== $value) {
                    return false;
                }
            }

            return true;
        } catch (NotFoundExceptionInterface $e) {
            return false;
        }
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

    /**
     * Retrieves a local embedded language entry.
     *
     * @param string $code The ISO 639-3 code of the language to retrieve.
     *
     * @return object An object that represents the language entry.
     *
     * @throws NotFoundExceptionInterface If no language exists for given code.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getLang(string $code)
    {
        return $this->languages->get($code);
    }

    /**
     * Retrieves a local embedded locale entry.
     *
     * @param string $code The BCP47 code of the locale to retrieve.
     *
     * @return object An object that represents the locale entry.
     *
     * @throws NotFoundExceptionInterface If no locale exists for given code.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getLocale(string $code)
    {
        return $this->locales->get($code);
    }
}
