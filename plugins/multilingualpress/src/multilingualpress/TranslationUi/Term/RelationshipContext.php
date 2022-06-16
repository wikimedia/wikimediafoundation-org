<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\TranslationUi\Term;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;

use function Inpsyde\MultilingualPress\combineAtts;

/**
 * Relationship context data object.
 */
class RelationshipContext
{
    const REMOTE_TERM_ID = 'remote_term_id';
    const REMOTE_SITE_ID = 'remote_site_id';
    const SOURCE_TERM_ID = 'source_term_id';
    const SOURCE_SITE_ID = 'source_site_id';

    const DEFAULTS = [
        self::REMOTE_TERM_ID => 0,
        self::REMOTE_SITE_ID => 0,
        self::SOURCE_TERM_ID => 0,
        self::SOURCE_SITE_ID => 0,
    ];

    /**
     * @var \WP_Term[]
     */
    private $terms = [];

    /**
     * @var array
     */
    private $data;

    /**
     * Returns a new context object, instantiated according to the data in the given context object
     * and the array.
     *
     * @param RelationshipContext $context
     * @param array $data
     * @return RelationshipContext
     */
    public static function fromExistingAndData(
        RelationshipContext $context,
        array $data
    ): RelationshipContext {

        $instance = new static();
        $instance->data = combineAtts($context->data, $data);

        if (
            !array_key_exists(self::SOURCE_TERM_ID, $data)
            && array_key_exists('source', $context->terms)
        ) {
            $instance->terms['source'] = $context->terms['source'];
        }

        if (
            !array_key_exists(self::REMOTE_TERM_ID, $data)
            && array_key_exists('remote', $context->terms)
        ) {
            $instance->terms['remote'] = $context->terms['remote'];
        }

        return $instance;
    }

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if (!is_array($this->data)) {
            $this->data = combineAtts(self::DEFAULTS, $data);
        }
    }

    /**
     * @return int
     */
    public function remoteTermId(): int
    {
        return (int)$this->data[static::REMOTE_TERM_ID];
    }

    /**
     * @return int
     */
    public function remoteSiteId(): int
    {
        return (int)$this->data[static::REMOTE_SITE_ID];
    }

    /**
     * @return int
     */
    public function sourceTermId(): int
    {
        return (int)$this->data[static::SOURCE_TERM_ID];
    }

    /**
     * @return int
     */
    public function sourceSiteId(): int
    {
        return (int)$this->data[static::SOURCE_SITE_ID];
    }

    /**
     * @return bool
     */
    public function hasRemoteTerm(): bool
    {
        return $this->remoteTerm() instanceof \WP_Term;
    }

    /**
     * @return \WP_Term|null
     */
    public function remoteTerm()
    {
        return $this->term($this->remoteSiteId(), 'remote');
    }

    /**
     * @return \WP_Term
     */
    public function sourceTerm(): \WP_Term
    {
        return $this->term($this->sourceSiteId(), 'source') ?: new \WP_Term(new \stdClass());
    }

    /**
     * Print HTML fields for the relationship context.
     * @param MetaboxFieldsHelper $helper
     */
    public function renderFields(MetaboxFieldsHelper $helper)
    {
        $baseName = $helper->fieldName('relation_context');
        $baseId = $helper->fieldId('relation_context');

        $fields = [
            RelationshipContext::SOURCE_SITE_ID => [$this, 'sourceSiteId'],
            RelationshipContext::SOURCE_TERM_ID => [$this, 'sourceTermId'],
            RelationshipContext::REMOTE_SITE_ID => [$this, 'remoteSiteId'],
            RelationshipContext::REMOTE_TERM_ID => [$this, 'remoteTermId'],
        ];

        foreach ($fields as $key => $callback) {
            ?>
            <input
                type="hidden"
                class="relationship-context-fields"
                name="<?= esc_attr("{$baseName}[{$key}]") ?>"
                id="<?= esc_attr("{$baseId}-{$key}") ?>"
                value="<?= esc_attr((string)$callback()) ?>">
            <?php
        }
    }

    /**
     * @param int $siteId
     * @param string $type
     * @return \WP_Term|null
     */
    private function term(int $siteId, string $type)
    {
        if (!array_key_exists($type, $this->terms)) {
            if (!$siteId) {
                $this->terms[$type] = null;
                return null;
            }

            $termTaxonomyId = $this->{"{$type}TermId"}();
            if (!$termTaxonomyId) {
                $this->terms[$type] = null;
                return null;
            }

            switch_to_blog($siteId);

            $terms = get_terms([
                'get' => 'all',
                'number' => 1,
                'update_term_meta_cache' => false,
                'orderby' => 'none',
                'suppress_filter' => true,
                'term_taxonomy_id' => $termTaxonomyId,
            ]);
            $this->terms[$type] = array_shift($terms);

            restore_current_blog();
        }

        return $this->terms[$type] ?: null;
    }
}
