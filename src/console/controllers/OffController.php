<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\console\controllers;

use Craft;
use craft\console\Controller;
use craft\helpers\Console;
use yii\console\ExitCode;

/**
 * Takes the system offline
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.5.7
 */
class OffController extends Controller
{
    /**
     * @var int|null Number of seconds that the `Retry-After` HTTP header should be set to for 503 responses
     */
    public $retry;

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'retry';
        return $options;
    }

    /**
     * Turns the system on
     *
     * @return int
     */
    public function actionIndex(): int
    {
        // If the isSystemLive config setting is set, then we can't control it from here
        if (is_bool($live = Craft::$app->getConfig()->getGeneral()->isSystemLive)) {
            $this->stderr('It\'s not possible to toggle the system status when the `isSystemLive` config setting is set.' . PHP_EOL, Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        // Temporarily allow changes to the project config even if it's supposed to be read only
        $projectConfig = Craft::$app->getProjectConfig();
        $readOnly = $projectConfig->readOnly;
        $projectConfig->readOnly = false;

        if (!Craft::$app->getIsLive()) {
            $this->stdout('The system is already offline.' . PHP_EOL, Console::FG_GREEN);
        } else {
            $projectConfig->set('system.live', false);
            $this->stdout('The system is now offline.' . PHP_EOL, Console::FG_GREEN);
        }

        if ($this->retry !== null) {
            $projectConfig->set('system.retryDuration', (int)$this->retry ?: null);
            $this->stdout(($this->retry ? "The retry duration is now set to $this->retry." : 'The retry duration has been removed.') . PHP_EOL, Console::FG_GREEN);
        }

        $projectConfig->readOnly = $readOnly;

        return ExitCode::OK;
    }
}
