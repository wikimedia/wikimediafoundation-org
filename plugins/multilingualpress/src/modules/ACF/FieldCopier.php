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
    const FIELD_TYPE_GROUP = 'group';
    const FIELD_TYPE_REPEATER = 'repeater';
    const FIELD_TYPE_FLEXIBLE = 'flexible_content';
    const FIELD_TYPE_IMAGE = 'image';
    const FIELD_TYPE_GALLERY = 'gallery';
    const FIELD_TYPE_TAXONOMY = 'taxonomy';
    const FIELD_TYPE_CLONE = 'clone';

    const FILE_FIELD_TYPES_FILTER = 'multilingualpress_acf_file_field_types_filter';
    const DEFAULT_FILE_FIELD_TYPES = ['file', 'video', 'image', 'application'];
    const COMPLEX_FIELDS = [self::FIELD_TYPE_FLEXIBLE, self::FIELD_TYPE_GROUP, self::FIELD_TYPE_REPEATER];

    /**
     * ACF flexible field's layout key
     * It is used to exclude the key from sync keys
     */
    const FLEXIBLE_FIELD_LAYOUT_KEY = 'acf_fc_layout';

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
        $fields = get_field_objects();

        if (empty($translation) || !$copyAcfFieldsIsChecked || !$fields) {
            return $keysToSync;
        }

        return $this->findACFFieldKeys($fields, $keysToSync, $context);
    }

    /**
     * This method will receive the ACF fields and
     * will find the appropriate meta keys depending on field type
     *
     * @param array $fields The list of advanced custom fields
     * @psalm-param array<Field> $fields The list of advanced custom fields
     * @param array $keys The list of meta keys
     * where should be added the ACF field keys to be synced
     * @param RelationshipContext $context
     * @return array The list of meta keys to be synced
     *
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
     * @throws NonexistentTable
     */
    protected function findACFFieldKeys(array $fields, array $keys, RelationshipContext $context): array
    {
        // phpcs:enable

        foreach ($fields as $fieldKey => $field) {
            switch ($field['type']) {
                case self::FIELD_TYPE_GROUP:
                case self::FIELD_TYPE_REPEATER:
                case self::FIELD_TYPE_FLEXIBLE:
                    $layouts = $this->findComplexFieldLayouts($field);
                    $parentKey = $this->findComplexFieldKey($field);
                    $value = $field['value'] ?? '';
                    $fieldName = $field['name'] ?? '';

                    if (empty($layouts) || !$value) {
                        break;
                    }
                    $layoutsStructure = $this->recursivelyFindLayoutStructure($layouts, $parentKey);
                    $foundKeys = $this->recursivelyFindLayoutFieldKeys($value, $fieldName, $context, $layoutsStructure, $fieldName);

                    foreach ($foundKeys as $value) {
                        $keys[] = $value;
                    }
                    $keys[] = $fieldKey;
                    break;
                case self::FIELD_TYPE_IMAGE:
                case in_array($field['type'], $this->acfFileFieldTypes, true):
                case self::FIELD_TYPE_GALLERY:
                    $keys[] = $fieldKey;
                    $this->handleFileTypeFieldsCopy((string)$field['type'], $field['value'], $context, $fieldKey);
                    break;
                case self::FIELD_TYPE_TAXONOMY:
                    $keys[] = $fieldKey;
                    $this->handleTaxTypeFieldsCopy((string)$field['type'], $field['value'], $context, $fieldKey);
                    break;
                default:
                    $keys[] = $fieldKey;
            }
        }

        return $keys;
    }

    /**
     * Recursively loop over the layout fields and generate the necessary keys and the types
     *
     * @param array $layouts The list of fields for which to find the structure
     * @psalm-param array<Field> $layouts The list of fields for which to find the structure
     * @param string $parentKey The parent field key to generate the key
     * @return array A map of the field key => field type
     */
    protected function recursivelyFindLayoutStructure(array $layouts, string $parentKey): array
    {
        $fieldsStructure = [];
        foreach ($layouts as $layout) {
            $parentKeyLayoutNamePart = $layout['name'] ? "{$parentKey}_{$layout['name']}" : $parentKey;
            $layoutParentKey = $parentKey ? $parentKeyLayoutNamePart : $layout['name'];
            $newKey = isset($layout['type']) ? $layoutParentKey : $parentKey;

            if (isset($layout['type'])) {
                $fieldsStructure[$newKey] = $layout['type'];
            }

            $subFields = $layout['sub_fields'] ?? ($layout['layouts'] ?? []);
            if (!empty($subFields)) {
                $fieldsStructure = array_merge($fieldsStructure, $this->recursivelyFindLayoutStructure($subFields, $newKey));
            }
        }

        return $fieldsStructure;
    }

    /**
     * This Method will recursively loop over the layout fields and will generate the necessary keys
     *
     * @param array $fields The list of fields
     * @psalm-param array<Field> $fields The list of fields
     * @param string $parentKey The key of the parent field to bind with the current key
     * @param RelationshipContext $context
     * @param array<string> $layoutsStructure A map of field structure key => field type
     * @param string $parentStructureKey The Parent field structure key
     * @return array<string> The list of the generated keys
     * @throws NonexistentTable
     */
    protected function recursivelyFindLayoutFieldKeys(
        array $fields,
        string $parentKey,
        RelationshipContext $context,
        array $layoutsStructure,
        string $parentStructureKey
    ): array {

        $keys = [];
        foreach ($fields as $key => $value) {
            $newKey = "{$parentKey}_{$key}";
            $structureKey = !is_int($key) ? "{$parentStructureKey}_{$key}" : $parentStructureKey;

            $fieldType = array_key_exists($structureKey, $layoutsStructure) ? $layoutsStructure[$structureKey] : '';

            $keys = array_merge($keys, $this->handleCloneTypeFieldsCopy($fieldType, (array)$value, $parentKey));
            $this->handleFileTypeFieldsCopy($fieldType, (array)$value, $context, $newKey);
            $this->handleTaxTypeFieldsCopy($fieldType, $value, $context, $newKey);

            if (is_array($value) && (in_array($fieldType, self::COMPLEX_FIELDS, true) || $fieldType === 'tab')) {
                $keys = array_merge($keys, $this->recursivelyFindLayoutFieldKeys($value, $newKey, $context, $layoutsStructure, $structureKey));
            }

            if ($key !== self::FLEXIBLE_FIELD_LAYOUT_KEY) {
                $keys[] = $newKey;
            }
        }

        return $keys;
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

        if ($fieldValue instanceof WP_Term) {
            $translations = translationIds($fieldValue->term_id, 'term');
            if (empty($translations[$remoteSiteId])) {
                return;
            }
            $connectedTaxIds[] = $translations[$remoteSiteId];
            $this->filterRemoteFieldValues($connectedTaxIds, $fieldKey);
            return;
        }

        foreach ($fieldValue as $tax) {
            $taxId = $tax instanceof WP_Term ? $tax->term_id : $tax;
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

    /**
     * The method will find a list of field layouts for complex fields (Repeater, Group, Flexible)
     *
     * @param array $field The map of field properties
     * @psalm-param Field $field The map of field properties
     * @psalm-return array<Field> A list of fields
     * @return array A list of fields
     */
    protected function findComplexFieldLayouts(array $field): array
    {
        if (empty($field['type'])) {
            return [];
        }

        return $field['type'] === self::FIELD_TYPE_GROUP || $field['type'] === self::FIELD_TYPE_REPEATER
            ? [$field]
            : ($field['layouts'] ?? []);
    }

    /**
     * The method will find the key for complex fields (Repeater, Group, Flexible)
     *
     * @param array $field The map of field properties
     * @psalm-param Field $field The map of field properties
     * @return string The field key
     */
    protected function findComplexFieldKey(array $field): string
    {
        if (empty($field['type'])) {
            return '';
        }

        return $field['type'] === self::FIELD_TYPE_GROUP  || $field['type'] === self::FIELD_TYPE_REPEATER
            ? ''
            : ($field['name'] ?? '');
    }

    /**
     * Get the correct field keys and values for Clone type fields
     *
     * @param string $fieldType The ACF field type, should be "clone"
     * @param array $value The clone field value which contains a map of cloned field keys and values
     * @param string $parentKey The parent key to correctly generate clone field keys
     * @return array The list of cloned field keys
     */
    protected function handleCloneTypeFieldsCopy(string $fieldType, array $value, string $parentKey): array
    {
        if ($fieldType !== self::FIELD_TYPE_CLONE) {
            return [];
        }

        $keys = [];
        foreach ($value as $cloneFieldKey => $cloneFieldValue) {
            if (empty($cloneFieldValue)) {
                continue;
            }
            $keys[] = "{$parentKey}_{$cloneFieldKey}";
        }

        return $keys;
    }
}
