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
 * Migrates a single MLP2 relationship to MLP3.
 *
 * @package MultilingualPress2to3
 */
class ContentRelationshipMigrator
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
     * Migrates an MLP2 relationship to MLP3.
     *
     * @param array|object $mlp2Relationship Data of an MLP2 relationship.
     *
     * @throws Throwable If problem migrating
     */
    public function migrate($mlp2Relationship)
    {
        $mlp2Relationship = (array) $mlp2Relationship;

        $sourceBlogId = (int) $mlp2Relationship['source_blog_id'];
        $sourceElementId = (int) $mlp2Relationship['source_element_id'];
        $destBlogId = (int) $mlp2Relationship['target_blog_id'];
        $destElementId = (int) $mlp2Relationship['target_element_id'];
        $relationshipType = $mlp2Relationship['type'];
        $groupId = $this->_getGroupId(
            $sourceBlogId,
            $sourceElementId,
            $destBlogId,
            $destElementId,
            $relationshipType
        );

        $this->_ensureRelationship($groupId, $sourceBlogId, $sourceElementId);
        $this->_ensureRelationship($groupId, $destBlogId, $destElementId);
    }

    /**
     * Retrieves a group ID to be used for an MLP2 link in MLP3.
     *
     * Because MLP2 uses groups to indicate that several entities are
     * related, this method will try to determine the group from existing
     * MLP3 data. If that is not possible, it will create a new group.
     *
     * @param int $sourceBlogId The blog ID of party A of the link.
     * @param int $sourceContentId The content ID of party A of the link.
     * @param int $destBlogId The blog ID of party B of the link.
     * @param int $destContentId The content ID of party B of the link.
     * @param string $typeCode The code of the link type.
     *
     * @return int The group ID.
     *
     * @throws Throwable If problem retrieving group ID.
     */
    protected function _getGroupId(
        $sourceBlogId,
        $sourceContentId,
        $destBlogId,
        $destContentId,
        $typeCode
    ): int {

        if (!($groupId = $this->_retrieveGroupId($sourceBlogId, $sourceContentId, $typeCode))) {
            $groupId = $this->_retrieveGroupId($destBlogId, $destContentId, $typeCode);
        }

        if (!$groupId) {
            $groupId = $this->_createGroup($typeCode);
            $this->_trigger('mlp2to3.group_created', ['group_id' => $groupId]);
        }

        return $groupId;
    }

    /**
     * Retrieves an MLP3 group ID for an entity.
     *
     * @param int $blogId Blog ID of the entity.
     * @param int $contentId Content ID of the entity (usually post or term ID).
     * @param string $relationshipType The type of the relationship to retrieve group ID for.
     *
     * @return int|null The relationship ID if found; null otherwise.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _retrieveGroupId($blogId, $contentId, $relationshipType)
    {
        // SELECT `relationship_id` FROM `mlp_content_relations` WHERE `site_id` = :blogId AND `content_id` = :contentId
        $relationshipsTable = $this->_getTableName('mlp_content_relations');
        $relationshipTableAlias = 'r';
        $relationshipIdField = 'relationship_id';
        $groupsTable = $this->_getTableName('mlp_relationships');
        $groupsTableAlias = 'g';
        $fields = $this->_getSelectFieldsString([
            "r.$relationshipIdField",
        ]);
        $query = sprintf(
            'SELECT %1$s
FROM %2$s %3$s
JOIN %4$s AS %5$s ON %6$s = %7$s
WHERE %8$s = %9$s AND %10$s = %11$s AND %12$s = %13$s
LIMIT 1',
            $fields,
            $this->_quoteIdentifier($relationshipsTable),
            $this->_quoteIdentifier($relationshipTableAlias),
            $this->_quoteIdentifier($groupsTable),
            $this->_quoteIdentifier($groupsTableAlias),
            $this->_quoteIdentifier("$groupsTableAlias.id"),
            $this->_quoteIdentifier("$relationshipTableAlias.relationship_id"),
            $this->_quoteIdentifier("$relationshipTableAlias.site_id"),
            '%d',
            $this->_quoteIdentifier("$relationshipTableAlias.content_id"),
            '%d',
            $this->_quoteIdentifier("$groupsTableAlias.type"),
            '%s'
        );

        $results = $this->_select(
            $query,
            [$blogId, $contentId, $relationshipType]
        );

        if (!count($results)) {
            return null;
        }

        $relationship = reset($results);

        if (!property_exists($relationship, $relationshipIdField)) {
            throw new UnexpectedValueException(
                $this->__(
                    'Relationship for blog "%1$s" and entity "%2$s" does not have a "%3$s" field',
                    [$blogId, $contentId, $relationshipIdField]
                )
            );
        }

        return (int) $relationship->{$relationshipIdField};
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
     * @param string $typeCode
     *
     * @return int The ID of the new group
     *
     * @throws Throwable If problem creating.
     */
    protected function _createGroup($typeCode)
    {
        // INSERT INTO `mlp_relationships` (`id`, `type`) VALUES (NULL, 'post')
        $table = $this->_getTableName('mlp_relationships');

        $id = $this->_insert(
            $table,
            [
                'id' => null,
                'type' => (string) $typeCode,
            ]
        );

        return $id;
    }

    /**
     * Adds an entity to a relationship (group).
     *
     * This is done by associating an entity with the group, and requires
     * a new record.
     *
     * @param int $groupId The group ID to associate the entity with.
     * @param int $siteId The entity blog ID.
     * @param int $contentId The entity content ID.
     *
     * @throws Throwable If problem creating relationship.
     */
    protected function _createRelationship(int $groupId, int $siteId, int $contentId)
    {
        // INSERT INTO `mlp_content_relations` (`relationship_id`, `site_id`, `content_id`) VALUES (:groupId, :siteId, :contentId)
        $table = $this->_getTableName('mlp_content_relations');

        $this->_insert(
            $table,
            [
                'relationship_id' => $groupId,
                'site_id' => $siteId,
                'content_id' => $contentId,
            ]
        );
    }

    /**
     * Ensures that an entity is associated with the specified group.
     *
     * Checks if the relationship exists, and creates it if not.
     *
     * @param int $groupId The group ID to ensure relationship with.
     * @param int $siteId The blog ID of the entity.
     * @param int $contentId The content ID of the entity.
     *
     * @throws Throwable If problem ensuring relationship.
     */
    protected function _ensureRelationship(int $groupId, int $siteId, int $contentId)
    {
        $table = $this->_getTableName('mlp_content_relations');
        $tableAlias = 'cr';
        $relationshipIdField = "$tableAlias.relationship_id";
        $siteIdField = "$tableAlias.site_id";
        $contentIdField = "$tableAlias.content_id";
        $fields = $this->_getSelectFieldsString([
            $relationshipIdField,
            $siteIdField,
            $contentIdField,
        ]);
        $query = sprintf(
            'SELECT %1$s FROM %2$s %3$s WHERE %4$s = %5$s AND %6$s = %7$s AND %8$s = %9$s',
            $fields,
            $this->_quoteIdentifier($table),
            $this->_quoteIdentifier($tableAlias),
            $this->_quoteIdentifier($relationshipIdField),
            '%d',
            $this->_quoteIdentifier($siteIdField),
            '%d',
            $this->_quoteIdentifier($contentIdField),
            '%d'
        );
        $result = $this->_select(
            $query,
            [$groupId, $siteId, $contentId]
        );

        $relationship = reset($result);
        if (!$relationship) {
            $this->_createRelationship($groupId, $siteId, $contentId);
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
}
