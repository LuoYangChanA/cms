<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\fieldlayoutelements;

use Craft;
use craft\base\ElementInterface;
use craft\base\FieldLayoutElement;
use craft\helpers\Html;
use craft\web\View;

/**
 * Template represents a UI element based on a custom template that can be included in field layouts.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.5.0
 */
class Template extends FieldLayoutElement
{
    /**
     * @var string The template path
     */
    public $template;

    /**
     * @inheritdoc
     */
    public function selectorHtml(): string
    {
        $text = Html::tag('div', Html::encode($this->template ?: Craft::t('app', 'Template')), [
            'class' => array_filter([
                'fld-element-label',
                $this->template ? 'code' : '',
            ]),
        ]);

        return <<<HTML
<div class="fld-template">
  <div class="fld-element-icon"></div>
  $text
</div>
HTML;
    }

    /**
     * @inheritdoc
     */
    public function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => Craft::t('app', 'Template'),
                'instructions' => Craft::t('app', 'The path to a template file within your site’s templates/ folder'),
                'class' => 'code',
                'id' => 'template',
                'name' => 'template',
                'value' => $this->template,
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function formHtml(ElementInterface $element = null, bool $static = false)
    {
        if (!$this->template) {
            return $this->_error(Craft::t('app', 'No template path has been chosen yet.'), 'warning');
        }

        try {
            $content = trim(Craft::$app->getView()->renderTemplate($this->template, [
                'element' => $element,
                'static' => $static,
            ], View::TEMPLATE_MODE_SITE));
        } catch (\Throwable $e) {
            return $this->_error($e->getMessage(), 'error');
        }

        if ($content === '') {
            return null;
        }

        return Html::tag('div', $content);
    }

    /**
     * Renders an error message.
     *
     * @param string $error
     * @param string $errorClass
     * @return string
     */
    private function _error(string $error, string $errorClass): string
    {
        $icon = Html::tag('span', '', [
            'data' => [
                'icon' => 'alert',
            ]
        ]);
        $content = Html::tag('p', $icon . ' ' . Html::encode($error), [
            'class' => $errorClass,
        ]);

        return Html::tag('div', $content, [
            'class' => 'pane',
        ]);
    }
}