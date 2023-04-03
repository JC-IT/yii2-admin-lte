<?php

namespace WolfpackIT\adminLte\widgets;

use WolfpackIT\adminLte\bundles\AdminLteBundle;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;
use yii\helpers\ArrayHelper;

/**
 * SideNavBar renders a navbar HTML component.
 *
 * Any content enclosed between the [[begin()]] and [[end()]] calls of NavBar
 * is treated as the content of the navbar. You may use widgets such as [[SideNav]]
 * to build up such content. For example,
 *
 * ```php
 * use common\themes\AdminLte\widgets\SideNavBar;
 * use common\themes\AdminLte\widgets\SideNav;
 *
 * SideNavBar::begin(['brandLabel' => 'NavBar Test']);
 * echo SideNav::widget([
 *     'items' => [
 *         ['label' => 'Home', 'url' => ['/site/index']],
 *         ['label' => 'About', 'url' => ['/site/about']],
 *     ],
 *     'options' => ['class' => 'navbar-nav'],
 * ]);
 * SideNavBar::end();
 * ```
 *
 * @property-write array $containerOptions
 *
 * Class SideBar
 * @package common\themes\AdminLte\widgets
 */
class SideNavBar extends Widget
{
    /**
     * @var array the HTML attributes for the widget container tag. The following special options are recognized:
     *
     * - tag: string, defaults to "nav", the name of the container tag.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var array
     */
    public $brandImageOptions = [];

    /**
     * @var string
     */
    public $brand;

    /**
     * @var string|bool the text of the brand or false if it's not used. Note that this is not HTML-encoded.
     * @see https://getbootstrap.com/docs/4.2/components/navbar/
     */
    public $brandLabel = false;

    /**
     * @var string|bool src of the brand image or false if it's not used. Note that this param will override `$this->brandLabel` param.
     * @see https://getbootstrap.com/docs/4.2/components/navbar/
     * @since 2.0.8
     */
    public $brandImage = false;

    /**
     * @var array|string|bool $url the URL for the brand's hyperlink tag. This parameter will be processed by [[\yii\helpers\Url::to()]]
     * and will be used for the "href" attribute of the brand link. Default value is false that means
     * [[\yii\web\Application::homeUrl]] will be used.
     * You may set it to `null` if you want to have no link at all.
     */
    public $brandUrl = false;

    /**
     * @var array the HTML attributes of the brand link.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $brandLinkOptions = [];

    /**
     * @var array the HTML attributes of the brand text.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $brandTextOptions = [];

    /**
     * @var string text to show for screen readers for the button to toggle the navbar.
     */
    public $screenReaderToggleText = 'Toggle navigation';

    /**
     * {@inheritdoc}
     */
    public $clientOptions = false;


    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        $navOptions = $this->options;
        if (empty($navOptions['class'])) {
            Html::addCssClass($navOptions, ['widget' => 'app-sidebar', 'bg-dark', 'shadow']);
        } else {
            Html::addCssClass($navOptions, ['widget' => 'app-sidebar']);
        }
        if (empty($navOptions['data']['bs-theme'])) {
            $navOptions['data']['bs-theme'] = 'dark';
        }

        $navTag = ArrayHelper::remove($navOptions, 'tag', 'aside') . "\n";

        if (isset($this->brand)) {
            $brand = $this->brand;
        } else {
            $brand = '';
            if ($this->brandImage !== false) {
                $brandImageOptions = $this->brandImageOptions;
                Html::addCssClass($brandImageOptions, ['widget' => 'brand-image']);
                $this->brandLabel = Html::img($this->brandImage, $brandImageOptions) . "\n";
            }
            if ($this->brandLabel !== false) {
                $brandTextOptions = $this->brandTextOptions;
                Html::addCssClass($brandTextOptions, ['widget' => 'brand-text']);
                $brandLinkOptions = $this->brandLinkOptions;
                Html::addCssClass($brandLinkOptions, ['widget' => 'brand-link']);
                if ($this->brandUrl === null) {
                    $brand = Html::a(
                        Html::tag('span', $this->brandLabel, $brandTextOptions),
                        '#',
                        $brandLinkOptions
                    );
                } else {
                    $brand = Html::a(
                        Html::tag('span', $this->brandLabel, $brandTextOptions),
                        $this->brandUrl === false ? \Yii::$app->homeUrl : $this->brandUrl,
                        $brandLinkOptions
                    );
                }
            }
        }

        echo Html::beginTag($navTag, $navOptions) . "\n";
        echo !empty($brand) ? Html::tag('div', $brand, ['class' => ['sidebar-brand']]) . "\n" : '';
        echo Html::beginTag('div', ['class' => ['sidebar-wrapper']]) . "\n";
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::endTag('div') . "\n";
        $navOptions = $this->options;
        $navTag = ArrayHelper::remove($navOptions, 'tag', 'aside');
        echo Html::endTag($navTag) . "\n";
        AdminLteBundle::register($this->getView());
    }
}
