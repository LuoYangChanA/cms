<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\elements\db;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\db\Table;
use craft\elements\Tag;
use craft\helpers\ArrayHelper;
use craft\helpers\Db;
use craft\models\TagGroup;
use yii\db\Connection;

/**
 * TagQuery represents a SELECT SQL statement for tags in a way that is independent of DBMS.
 *
 * @property string|string[]|TagGroup $group The handle(s) of the tag group(s) that resulting tags must belong to.
 * @method Tag[]|array all($db = null)
 * @method Tag|array|null one($db = null)
 * @method Tag|array|null nth(int $n, Connection $db = null)
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 * @doc-path tags.md
 * @supports-site-params
 * @supports-title-param
 * @supports-uri-param
 * @replace {element} tag
 * @replace {elements} tags
 * @replace {twig-method} craft.tags()
 * @replace {myElement} myTag
 * @replace {element-class} \craft\elements\Tag
 */
class TagQuery extends ElementQuery
{
    /**
     * @inheritdoc
     */
    protected $defaultOrderBy = ['content.title' => SORT_ASC];

    // General parameters
    // -------------------------------------------------------------------------

    /**
     * @var int|int[]|null|false The tag group ID(s) that the resulting tags must be in.
     * ---
     * ```php
     * // fetch tags in the Topics group
     * $tags = \craft\elements\Tag::find()
     *     ->group('topics')
     *     ->all();
     * ```
     * ```twig
     * {# fetch tags in the Topics group #}
     * {% set tags = craft.tags()
     *   .group('topics')
     *   .all() %}
     * ```
     * @used-by group()
     * @used-by groupId()
     */
    public $groupId;

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($name === 'group') {
            $this->group($value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Narrows the query results based on the tag groups the tags belong to.
     *
     * Possible values include:
     *
     * | Value | Fetches tags…
     * | - | -
     * | `'foo'` | in a group with a handle of `foo`.
     * | `'not foo'` | not in a group with a handle of `foo`.
     * | `['foo', 'bar']` | in a group with a handle of `foo` or `bar`.
     * | `['not', 'foo', 'bar']` | not in a group with a handle of `foo` or `bar`.
     * | a [[TagGroup|TagGroup]] object | in a group represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch tags in the Foo group #}
     * {% set {elements-var} = {twig-method}
     *   .group('foo')
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch tags in the Foo group
     * ${elements-var} = {php-method}
     *     ->group('foo')
     *     ->all();
     * ```
     *
     * @param string|string[]|TagGroup|null $value The property value
     * @return static self reference
     * @uses $groupId
     */
    public function group($value)
    {
        if ($value instanceof TagGroup) {
            $this->groupId = [$value->id];
        } else if ($value !== null) {
            $this->groupId = (new Query())
                ->select(['id'])
                ->from([Table::TAGGROUPS])
                ->where(Db::parseParam('handle', $value))
                ->column() ?: false;
        } else {
            $this->groupId = null;
        }

        return $this;
    }

    /**
     * Narrows the query results based on the tag groups the tags belong to, per the groups’ IDs.
     *
     * Possible values include:
     *
     * | Value | Fetches tags…
     * | - | -
     * | `1` | in a group with an ID of 1.
     * | `'not 1'` | not in a group with an ID of 1.
     * | `[1, 2]` | in a group with an ID of 1 or 2.
     * | `['not', 1, 2]` | not in a group with an ID of 1 or 2.
     *
     * ---
     *
     * ```twig
     * {# Fetch tags in the group with an ID of 1 #}
     * {% set {elements-var} = {twig-method}
     *   .groupId(1)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch tags in the group with an ID of 1
     * ${elements-var} = {php-method}
     *     ->groupId(1)
     *     ->all();
     * ```
     *
     * @param int|int[]|null $value The property value
     * @return static self reference
     * @uses $groupId
     */
    public function groupId($value)
    {
        $this->groupId = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {
        $this->_normalizeGroupId();

        $this->joinElementTable('tags');

        $this->query->select([
            'tags.groupId',
        ]);

        if ($this->groupId) {
            $this->subQuery->andWhere(['tags.groupId' => $this->groupId]);
        }

        return parent::beforePrepare();
    }

    /**
     * Normalizes the groupId param to an array of IDs or null
     *
     * @throws QueryAbortedException
     */
    private function _normalizeGroupId()
    {
        if ($this->groupId === false) {
            throw new QueryAbortedException();
        }

        if (empty($this->groupId)) {
            $this->groupId = null;
        } else if (is_numeric($this->groupId)) {
            $this->groupId = [$this->groupId];
        } else if (!is_array($this->groupId) || !ArrayHelper::isNumeric($this->groupId)) {
            $this->groupId = (new Query())
                ->select(['id'])
                ->from([Table::TAGGROUPS])
                ->where(Db::parseParam('id', $this->groupId))
                ->column();
        }
    }

    /**
     * @inheritdoc
     * @since 3.5.0
     */
    protected function cacheTags(): array
    {
        $tags = [];
        if ($this->groupId) {
            foreach ($this->groupId as $groupId) {
                $tags[] = "group:$groupId";
            }
        }
        return $tags;
    }
}
