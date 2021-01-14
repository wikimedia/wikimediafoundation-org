<?php # -*- coding: utf-8 -*-
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
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post\MetaboxFields;

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
    const FIELD_TYPE_FILE = 'file';

    /**
     * ACF flexible field's layout key
     * It is used to exclude the key from sync keys
     */
    const FLEXIBLE_FIELD_LAYOUT_KEY = 'acf_fc_layout';

    /**
     * @var Copier
     */
    protected $copier;

    public function __construct(Copier $copier)
    {
        $this->copier = $copier;
    }

    /**
     * The Method is a callback for PostRelationSaveHelper::FILTER_SYNC_KEYS filter
     * It will receive the keys of the meta fields which should be synced and
     * will add the ACF field keys
     *
     * @param array $keysToSync the array of meta keys
     * where should be added the ACF field keys to be synced
     * @param RelationshipContext $context
     * @param Request $request
     * @return array the array of meta keys to be synced
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

        if (empty($translation) || !$copyAcfFieldsIsChecked || empty($fields)) {
            return $keysToSync;
        }

        $keysToSync = $this->addACFFieldKeys($fields, $keysToSync, $context);

        return $keysToSync;
    }

    /**
     * This method will receive the ACF fields and
     * will find the appropriate meta keys depending on field type
     *
     * @param array $fields the array of advanced custom fields
     * @param array $keys the array of meta keys
     * where should be added the ACF field keys to be synced
     * @param RelationshipContext $context
     * @return array the array of meta keys to be synced
     *
     * * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     * * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
     */
    protected function addACFFieldKeys(array $fields, array $keys, RelationshipContext $context): array
    {
        // phpcs:enable
        foreach ($fields as $filedKey => $field) {
            switch ($field['type']) {
                case self::FIELD_TYPE_GROUP:
                case self::FIELD_TYPE_REPEATER:
                case self::FIELD_TYPE_FLEXIBLE:
                    if (empty($field['value']) || empty($field['name'])) {
                        break;
                    }
                    $foundKeys = $this->recursivelyFindLayoutFieldKeys($field['value'], $field['name']);
                    foreach ($foundKeys as $key => $value) {
                        $keys[] = $value;
                    }
                    $keys[] = $filedKey;
                    break;
                case self::FIELD_TYPE_IMAGE:
                case self::FIELD_TYPE_FILE:
                    $attachmentIds[] = $field['value']['id'] ?? '';
                    $keys[] = $filedKey;
                    $remoteAttachmentIds = $this->copyAttachments($context, $attachmentIds);
                    $this->filterRemoteAttachmentValues($remoteAttachmentIds, $filedKey);
                    break;
                case self::FIELD_TYPE_GALLERY:
                    $attachmentIds = [];
                    $keys[] = $filedKey;
                    foreach ($field['value'] as $attachment) {
                        $attachmentIds[] = $attachment['id'] ?? '';
                    }
                    $remoteAttachmentGalleryIds[] = $this->copyAttachments($context, $attachmentIds);
                    $this->filterRemoteAttachmentValues($remoteAttachmentGalleryIds, $filedKey);
                    break;
                default:
                    $keys[] = $filedKey;
            }
        }

        return $keys;
    }

    /**
     * This Method will recursively loop over the layout fields and will generate the necessary keys
     *
     * @param array $array the array of fields
     * @param string $parentKey The key of the parent field to bind with the current key
     * @return array the array of the generated keys
     */
    protected function recursivelyFindLayoutFieldKeys(array $array, string $parentKey): array
    {
        $keys = [];
        foreach ($array as $key => $value) {
            $newKey = $parentKey . '_' . $key;

            if (is_array($array[$key])) {
                $keys = array_merge($keys, $this->recursivelyFindLayoutFieldKeys($array[$key], $newKey));
            }

            if ($key !== self::FLEXIBLE_FIELD_LAYOUT_KEY) {
                $keys[] = $newKey;
            }
        }

        return $keys;
    }

    /**
     * The Method will copy the attachments from source site to the remote site
     *
     * @param RelationshipContext $context
     * @param array $attachmentIds The attachment IDs which should be copied to remote entity
     * @return array array of the attachment IDs in remote site which are copied from source site
     */
    protected function copyAttachments(RelationshipContext $context, array $attachmentIds): array
    {
        if (empty($attachmentIds)) {
            return [];
        }
        $sourceSiteId = $context->sourceSiteId();
        $remoteSiteId = $context->remoteSiteId();

        return $remoteAttachmentIds = $this->copier->copyById(
            $sourceSiteId,
            $remoteSiteId,
            $attachmentIds
        );
    }

    /**
     * The Method will filter the values of the ACF fields in remote site and will replace them with
     * the correct attachment ids which are copied from source site
     *
     * @param array $remoteAttachmentIds The attachment IDs in remote site which
     * are copied from source site
     * @param string $filedKey The ACF field Key of the remote site
     * for which the value should be filtered
     */
    protected function filterRemoteAttachmentValues(array $remoteAttachmentIds, string $filedKey)
    {
        add_filter(
            PostRelationSaveHelper::FILTER_METADATA,
            function ($valuesToSync) use ($remoteAttachmentIds, $filedKey) {
                if (!empty($valuesToSync) && !empty($valuesToSync[$filedKey])) {
                    $valuesToSync[$filedKey] = $remoteAttachmentIds;
                }
                return $valuesToSync;
            },
            10,
            2
        );
    }
}
