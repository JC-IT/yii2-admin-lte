<?php

declare(strict_types=1);

namespace WolfpackIT\adminLte\widgets;

use kartik\icons\Icon;
use WolfpackIT\adminLte\bundles\AdminLteBundle;
use yii\base\InvalidConfigException;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\helpers\ArrayHelper;

class SideNav extends Nav
{
    public $dropdownClass = self::class;
    public array $iconOptions = [];
    public bool $isSubMenu = false;
    public array $navOptions = [];

    protected function hasActiveChild(array $items): bool
    {
        foreach ($items as $i => $child) {
            if (is_array($child) && !ArrayHelper::getValue($child, 'visible', true)) {
                continue;
            }
            if ($this->isItemActive($child)) {
                return true;
            }
            $childItems = ArrayHelper::getValue($child, 'items');
            if (is_array($childItems)) {
                $activeParent = false;
                $this->isChildActive($childItems, $activeParent);
                if ($activeParent) {
                    return true;
                }
            }
        }

        return false;
    }

    public function init(): void
    {
        parent::init();

        Html::addCssClass($this->options, ['sidebar-menu', 'flex-column']);
        $this->options['data-lte-toggle'] = 'treeview';
        $this->options['role'] = 'menu';
        $this->options['data-accordion'] = 'false';
    }

    /**
     * @param array|string $item
     */
    public function renderItem($item): string
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $disabled = ArrayHelper::getValue($item, 'disabled', false);
        $active = $this->isItemActive($item);

        $iconOptions = ArrayHelper::remove($item, 'iconOptions', []);
        $iconOptions = ArrayHelper::merge($this->iconOptions, $iconOptions);
        Html::addCssClass($iconOptions, 'nav-icon');
        $emptyIconOptions = ['style' => ['color' => 'transparent']];
        $iconHtml = ArrayHelper::getValue($item, 'iconHtml');
        if (empty($iconHtml)) {
            $iconHtml = isset($item['icon'])
                ? Icon::show($item['icon'], $iconOptions)
                : Icon::show('circle', ArrayHelper::merge($iconOptions, $emptyIconOptions));
        }
        if (empty($items)) {
            $subMenu = '';
            $showSubmenu = '';
        } else {
            Html::addCssClass($options, ['has-treeview']);
            $showSubmenu = Icon::show('angle-left', ['class' => 'nav-treeview-status-icon']);

            $dropdownOptions = ArrayHelper::getValue($item, 'dropdownOptions', []);
            Html::addCssClass($dropdownOptions, ['nav-treeview']);
            $item['dropdownOptions'] = $dropdownOptions;

            if (is_array($items)) {
                $items = $this->isChildActive($items, $active);
                $subMenu = $this->renderDropdown($items, $item);
            }
        }

        Html::addCssClass($options, 'nav-item');
        Html::addCssClass($linkOptions, 'nav-link');

        if ($disabled) {
            ArrayHelper::setValue($linkOptions, 'tabindex', '-1');
            ArrayHelper::setValue($linkOptions, 'aria-disabled', 'true');
            Html::addCssClass($linkOptions, 'disabled');
        } elseif ($this->activateItems && $active) {
            Html::addCssClass($linkOptions, 'active');
        }

        if (is_array($items) && $this->hasActiveChild($items)) {
            Html::addCssClass($options, ['menu-open']);
        }

        return Html::tag('li', Html::a($iconHtml . Html::tag('p', $label  . $showSubmenu), $url, $linkOptions) . $subMenu, $options);
    }

    protected function renderDropdown(array $items, array $parentItem): string
    {
        /** @var self $dropdownClass */
        $dropdownClass = $this->dropdownClass;
        return $dropdownClass::widget([
            'options' => ArrayHelper::getValue($parentItem, 'dropdownOptions', []),
            'items' => $items,
            'encodeLabels' => $this->encodeLabels,
            'clientOptions' => false,
            'view' => $this->getView(),
            'isSubMenu' => true
        ]);
    }

    public function renderItems(): string
    {
        $result = (!$this->isSubMenu ? Html::beginTag('nav') : '') . "\n";
        $result .= parent::renderItems();
        $result .= (!$this->isSubMenu ? Html::endTag('nav') : '') . "\n";
        return $result;
    }

    public function run(): string
    {
        AdminLteBundle::register($this->getView());
        return parent::run();
    }
}
