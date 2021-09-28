<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\events;

use craft\elements\Asset;
use craft\models\AssetTransform;
use yii\base\Event;

/**
 * Define asset URL event class
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 4.0.0
 */
class DefineAssetUrlEvent extends Event
{
    /**
     * @var AssetTransform|string|array|null Asset transform index that is being generated (if any)
     */
    public $transform;

    /**
     * @var Asset The asset that is being transformed.
     */
    public Asset $asset;

    /**
     * @var string|null Url to requested Asset that should be used instead.
     */
    public ?string $url = null;
}
