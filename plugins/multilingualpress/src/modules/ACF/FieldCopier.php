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

namespace Inpsyde\MultilingualPress\Module\ACF;

use Inpsyde\MultilingualPress\Attachment\Copier;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use WP_Term;

use function Inpsyde\MultilingualPress\translationIds;

/**
 * @psalm-type FieldType = 'repeater'|'group'|'flexible_content'|'image'|'gallery'|'taxonomy'|'clone'|'simple'
 * @psalm-type Field = array{name: string, value: mixed, type?: FieldType, layouts?: array<Field>}
 */
class FieldCopier
{
    /**
     * ACF field Types
     */
    const FIELD_TYPE_IMAGE = 'image';
    const FIELD_TYPE_GALLERY = 'gallery';
    const FIELD_TYPE_TAXONOMY = 'taxonomy';

    const FILE_FIELD_TYPES_FILTER = 'multilingualpress_acf_file_field_types_filter';
    const DEFAULT_FILE_FIELD_TYPES = ['file', 'video', 'image', 'application'];

    /**
     * @var Copier
     */
    protected $copier;

    /**
     * @var array
     */
    private $acfFileFieldTypes;

    public function __construct(Copier $copier)
    {
        $this->copier = $copier;
        $this->acfFileFieldTypes = apply_filters(
            self::FILE_FIELD_TYPES_FILTER,
            self::DEFAULT_FILE_FIELD_TYPES
        );
    }

    /**
     * Handle the copy of ACF Fields
     *
     * The Method is a callback for PostRelationSaveHelper::FILTER_SYNC_KEYS filter
     * It will receive the keys of the meta fields which should be synced and
     * will add the ACF field keys
     *
     * @param array $keysToSync The list of meta keys
     * where should be added the ACF field keys to be synced
     * @param RelationshipContext $context
     * @param Request $request
     * @return array The list of meta keys to be synced
     * @throws NonexistentTable
     */
    public function handleCopyACFFields(
        array $keysToSync,
        RelationshipContext $context,
        Request $request
    ): array {

        $multilingualpress = $request->bodyValue(
            'multilingualpress',
            INPUT_POST,
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );

        $remoteSiteId = $context->remoteSiteId();
        $translation = $multilingualpress["site-{$remoteSiteId}"] ?? '';
        $copyAcfFieldsIsChecked = $translation[MetaboxFields::FIELD_COPY_ACF_FIELDS] ?? 0;
        // Let's keep this in order to be able to bail early if there are no ACF fields for the current post
        $fields = get_field_objects();

        if (empty($translation) || !$copyAcfFieldsIsChecked || !$fields) {
            return $keysToSync;
        }

        $acfFieldObjects = $this->getACFFieldObjects(get_the_ID());
        $acfMetaKeys = $this->extractACFFieldMetaKeys($acfFieldObjects);
        $this->handleSpecialACFFieldTypes($acfFieldObjects, $context);
        return array_merge($keysToSync, $acfMetaKeys);
    }

    /**
     * Gets the ACF field objects.
     *
     * Gets the ACF field object based on post meta key
     *
     * @param int $postId The id for the post for which to get ACF field objects
     * @return array The list of advanced custom fields
     * @psalm-return array<Field> The list of advanced custom fields
     */
    private function getACFFieldObjects(int $postId): array
    {
        $acfFieldObjects = [];
        $allPostMetaForPost = get_post_meta($postId);

        foreach ($allPostMetaForPost as $singlePostMetaKey => $singlePostMetaValue) {
            $acfFieldObject = get_field_object($singlePostMetaKey);

            // get_field_object returns false if the meta key is not an ACF field
            if (empty($acfFieldObject)) {
                continue;
            }

            $acfFieldObjects[] = $acfFieldObject;
        }

        return $acfFieldObjects;
    }

    /**
     * Extract all meta keys from the list of ACF fields.
     *
     * We need a list of ACF post meta keys, including field key reference that each field has,
     * cause this way the data for the target page will be complete immediately and the editor
     * wont have to save the page in order for ACF content to show up in the frontend.
     *
     * @param array $acfFieldObjects The list of advanced custom fields
     * @psalm-param array<Field> $acfFieldObjects The list of advanced custom fields
     * @return array<string> The list of ACF post meta keys
     */
    protected function extractACFFieldMetaKeys(array $acfFieldObjects): array
    {
        $acfFieldMetaKeys = [];

        foreach ($acfFieldObjects as $acfFieldObject) {
            $acfFieldMetaKeys[] = $acfFieldObject['name'];
            $acfFieldMetaKeys[] = "_{$acfFieldObject['name']}";
        }

        return $acfFieldMetaKeys;
    }

    /**
     * Deals with ACF field types that need special handling such as files and taxonomies.
     *
     * @param array $acfFieldObjects The list of advanced custom fields
     * @psalm-param array<Field> $acfFieldObjects The list of advanced custom fields
     * @param RelationshipContext $context
     * @throws NonexistentTable
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    private function handleSpecialACFFieldTypes(array $acfFieldObjects, RelationshipContext $context)
    {
        // phpcs:enable

        foreach ($acfFieldObjects as $acfFieldObject) {
            if (!isset($acfFieldObject['type']) || !isset($acfFieldObject['key'])) {
                return;
            }

            switch ($acfFieldObject['type']) {
                case self::FIELD_TYPE_IMAGE:
                case self::FIELD_TYPE_GALLERY:
                case in_array($acfFieldObject['type'], $this->acfFileFieldTypes, true):
                    $this->handleFileTypeFieldsCopy(
                        $acfFieldObject['type'],
                        (array)$acfFieldObject['value'],
                        $context,
                        $acfFieldObject['name']
                    );
                    break;
                case self::FIELD_TYPE_TAXONOMY:
                    $this->handleTaxTypeFieldsCopy(
                        $acfFieldObject['type'],
                        (array)$acfFieldObject['value'],
                        $context,
                        $acfFieldObject['name']
                    );
                    break;
            }
        }
    }

    /**
     * The method will handle the Taxonomy type fields copy process
     *
     * @param string $fieldType The ACF field type, should be image, gallery or file
     * @param array|string|WP_Term $fieldValue The value of taxonomy field
     * @param RelationshipContext $context
     * @param string $fieldKey The ACF field key
     * @throws NonexistentTable
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    protected function handleTaxTypeFieldsCopy(
        string $fieldType,
        $fieldValue,
        RelationshipContext $context,
        string $fieldKey
    ) {

        // phpcs:enable

        if ($fieldType !== self::FIELD_TYPE_TAXONOMY || !$fieldValue) {
            return;
        }

        $remoteSiteId = $context->remoteSiteId();
        $connectedTaxIds = [];

        if ($fieldValue instanceof WP_Term || isset($fieldValue['term_id'])) {
            $termId = $fieldValue->term_id ?? $fieldValue['term_id'];
            $translations = translationIds($termId, 'term');
            if (empty($translations[$remoteSiteId])) {
                return;
            }
            $connectedTaxIds[] = $translations[$remoteSiteId];
            $this->filterRemoteFieldValues($connectedTaxIds, $fieldKey);
            return;
        }

        foreach ($fieldValue as $tax) {
            $taxId = $tax instanceof WP_Term ? $tax->term_id : $tax;
            if (!$taxId) {
                continue;
            }

            $translations = translationIds($taxId, 'term');
            if (empty($translations[$remoteSiteId])) {
                continue;
            }
            $connectedTaxIds[0][] = $translations[$remoteSiteId];
        }

        $this->filterRemoteFieldValues($connectedTaxIds, $fieldKey);
    }

    /**
     * The method will handle the file type fields(image, gallery, file) copy process
     *
     * @param string $fieldType The ACF field type, should be image, gallery or file
     * @param array $fieldValue The ACF field value
     * @param RelationshipContext $context
     * @param string $fieldKey The ACF field key
     */
    protected function handleFileTypeFieldsCopy(
        string $fieldType,
        array $fieldValue,
        RelationshipContext $context,
        string $fieldKey
    ) {

        if (
            !in_array($fieldType, $this->acfFileFieldTypes, true) &&
            $fieldType !== self::FIELD_TYPE_GALLERY
        ) {
            return;
        }

        $attachmentIds[] = $fieldValue['id'] ?? $fieldValue[0] ?? '';
        $remoteAttachmentIds = $this->copyAttachments($context, $attachmentIds);

        if ($fieldType === self::FIELD_TYPE_GALLERY && !empty($fieldValue)) {
            $attachmentIds = [];
            foreach ($fieldValue as $attachment) {
                $attachmentIds[] = $attachment['id'] ?? $attachment ?? '';
            }

            $remoteAttachmentIds[] = $this->copyAttachments($context, $attachmentIds);
        }
        $this->filterRemoteFieldValues($remoteAttachmentIds, $fieldKey);
    }

    /**
     * The Method will copy the attachments from source site to the remote site
     *
     * @param RelationshipContext $context
     * @param array $attachmentIds The list of attachment IDs which should be copied to remote entity
     * @return array The list of the attachment IDs in remote site which are copied from source site
     */
    protected function copyAttachments(RelationshipContext $context, array $attachmentIds): array
    {
        if (empty($attachmentIds)) {
            return [];
        }
        $sourceSiteId = $context->sourceSiteId();
        $remoteSiteId = $context->remoteSiteId();

        return $this->copier->copyById(
            $sourceSiteId,
            $remoteSiteId,
            $attachmentIds
        );
    }

    /**
     * Filter the values of the ACF fields
     *
     * The Method will filter the values of the ACF fields in remote site and will replace them with
     * the correct attachment ids which are copied from source site
     *
     * @param array $values The values which should be replaced in remote site fields
     * @param string $filedKey The ACF field Key of the remote site
     * for which the value should be filtered
     */
    protected function filterRemoteFieldValues(array $values, string $filedKey)
    {
        add_filter(
            PostRelationSaveHelper::FILTER_METADATA,
            static function ($valuesToSync) use ($values, $filedKey) {
                if (!empty($valuesToSync) && isset($valuesToSync[$filedKey])) {
                    $valuesToSync[$filedKey] = $values;
                }

                return $valuesToSync;
            },
            10,
            2
        );
    }
}
