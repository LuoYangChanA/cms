<?php
/**
 * The base class for all asset Volumes. All Volume types must extend this class.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */

namespace craft\base;

use Craft;
use craft\behaviors\FieldLayoutBehavior;
use craft\elements\Asset;
use craft\helpers\Assets;
use craft\models\FieldLayout;
use craft\records\Volume as VolumeRecord;
use craft\validators\HandleValidator;
use craft\validators\UniqueValidator;

/**
 * Volume is the base class for classes representing volumes in terms of objects.
 *
 * @mixin FieldLayoutBehavior
 */
abstract class Volume extends SavableComponent implements VolumeInterface
{
    use VolumeTrait;

    /* @since 4.0.0 */
    public const CONFIG_MIMETYPE = 'mimetype';
    /* @since 4.0.0 */
    public const CONFIG_VISIBILITY = 'visibility';

    /* @since 4.0.0 */
    public const VISIBILITY_DEFAULT = 'default';
    /* @since 4.0.0 */
    public const VISIBILITY_HIDDEN = 'hidden';
    /* @since 4.0.0 */
    public const VISIBILITY_PUBLIC = 'public';

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['fieldLayout'] = [
            'class' => FieldLayoutBehavior::class,
            'elementType' => Asset::class,
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'url' => Craft::t('app', 'URL'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['id', 'fieldLayoutId'], 'number', 'integerOnly' => true];
        $rules[] = [['name', 'handle'], UniqueValidator::class, 'targetClass' => VolumeRecord::class];
        $rules[] = [['hasUrls'], 'boolean'];
        $rules[] = [['name', 'handle', 'url'], 'string', 'max' => 255];
        $rules[] = [['name', 'handle'], 'required'];
        $rules[] = [
            ['handle'],
            HandleValidator::class,
            'reservedWords' => [
                'dateCreated',
                'dateUpdated',
                'edit',
                'id',
                'title',
                'uid',
            ],
        ];
        $rules[] = [['fieldLayout'], 'validateFieldLayout'];

        // Require URLs for public Volumes.
        if ($this->hasUrls) {
            $rules[] = [['url'], 'required'];
        }

        return $rules;
    }

    /**
     * Validates the field layout.
     *
     * @since 3.7.0
     */
    public function validateFieldLayout(): void
    {
        $fieldLayout = $this->getFieldLayout();
        $fieldLayout->reservedFieldHandles = [
            'folder',
            'volume',
        ];

        if (!$fieldLayout->validate()) {
            $this->addModelErrors($fieldLayout, 'fieldLayout');
        }
    }

    /**
     * @inheritdoc
     * @since 3.5.0
     */
    public function getFieldLayout(): ?FieldLayout
    {
        /** @var FieldLayoutBehavior $behavior */
        $behavior = $this->getBehavior('fieldLayout');
        return $behavior->getFieldLayout();
    }

    /**
     * @inheritdoc
     */
    public function getRootUrl(): ?string
    {
        if (!$this->hasUrls) {
            return null;
        }

        return rtrim(Craft::parseEnv($this->url), '/') . '/';
    }

    /**
     * @inheritdoc
     */
    public function saveFileLocally(string $uriPath, string $targetPath): int
    {
        return Assets::downloadFile($this, $uriPath, $targetPath);
    }

    /**
     * @inheritdoc
     */
    public function createFileByStream(string $path, $stream, array $config): void
    {
        $this->writeFileFromStream($path, $stream, $config);
    }

    /**
     * @inheritdoc
     */
    public function updateFileByStream(string $path, $stream, array $config): void
    {
        $this->writeFileFromStream($path, $stream, $config);
    }
}
