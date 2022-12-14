<?php
namespace andresbreads\dashlite\widgets;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Html;

/**
 * {@inheritDoc}
 */
class Nav extends \yii\bootstrap5\Nav
{
    /**
     * @inheritdoc
     */
    public $options = [
        'class' => 'nk-menu nk-menu-main',
        'role' => 'menu',
    ];

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        Html::removeCssClass($this->options, ['widget' => 'nav']);
    }

    /**
     * {@inheritDoc}
     */
    public function renderItem($item): string
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = $item['encode'] ?? $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $disabled = ArrayHelper::getValue($item, 'disabled', false);
        $active = $this->isItemActive($item);

        Html::addCssClass($options, 'nk-menu-item');
        Html::addCssClass($linkOptions, 'nk-menu-link');
        $label = Html::tag('span', $label, ['class' => 'nk-menu-text']);

        if (empty($items)) {
            $items = '';
            Html::addCssClass($options, ['widget' => 'nav-item']);
            Html::addCssClass($linkOptions, ['widget' => 'nav-link']);
        } else {
            $linkOptions['data']['bs-toggle'] = 'dropdown';
            $linkOptions['role'] = 'button';
            $linkOptions['aria']['expanded'] = 'false';
            Html::addCssClass($options, ['widget' => 'dropdown nav-item']);
            Html::addCssClass($linkOptions, ['widget' => 'dropdown-toggle nav-link']);
            if (is_array($items)) {
                $items = $this->isChildActive($items, $active);
                $items = $this->renderDropdown($items, $item);
            }
        }

        if ($disabled) {
            ArrayHelper::setValue($linkOptions, 'tabindex', '-1');
            ArrayHelper::setValue($linkOptions, 'aria.disabled', 'true');
            Html::addCssClass($linkOptions, ['disable' => 'disabled']);
        } elseif ($this->activateItems && $active) {
            Html::addCssClass($linkOptions, ['activate' => 'active']);
        }

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }
}