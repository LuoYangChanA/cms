<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\web\assets\garnish;

use craft\web\assets\elementresizedetector\ElementResizeDetectorAsset;
use craft\web\assets\jquerytouchevents\JqueryTouchEventsAsset;
use craft\web\assets\velocity\VelocityAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Garnish asset bundle.
 */
class GarnishAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/dist';

    /**
     * @inheritdoc
     */
    public $js = [
        'garnish.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        ElementResizeDetectorAsset::class,
        JqueryAsset::class,
        JqueryTouchEventsAsset::class,
        VelocityAsset::class,
    ];
}
