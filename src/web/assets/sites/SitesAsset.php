<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\web\assets\sites;

use craft\web\assets\cp\CpAsset;
use craft\web\assets\vue\VueAsset;
use yii\web\AssetBundle;

/**
 * Asset bundle for the Sites page
 */
class SitesAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/dist';

    /**
     * @inheritdoc
     */
    public $depends = [
        CpAsset::class,
        VueAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'SitesAdmin.min.js',
    ];
}
