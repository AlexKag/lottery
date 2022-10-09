<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\widgets;

use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Description of DbCarouselNews
 *
 * @author fistashkin
 */
class DbCarouselNews extends DbCarousel
{

    public $showIndicators = true;
    public $prefix = '<div class="banner__top">Главные новости</div>';
    public $caption = 'Главные новости';
    public $itemsOptions;

    public function init()
    {
        parent::init();
        Html::removeCssClass($this->options, ['widget' => 'carousel']);
//        print_r($this->options);die;
        foreach ($this->items as $key => $item) {
            $this->items[$key]['options'] = $this->itemsOptions;
        }
    }

    /**
     * Renders carousel items as specified on [[items]].
     * @return string the rendering result
     */
    public function renderItems()
    {
        $items = [];
        for ($i = 0, $count = count($this->items); $i < $count; $i++) {
            $items[] = $this->renderItem($this->items[$i], $i);
        }

        return Html::tag('div', implode("\n", $items), ['class' => 'banner__wrap']);
    }

    /**
     * Renders carousel indicators.
     * @return string the rendering result
     */
    public function renderIndicators()
    {
        if ($this->showIndicators === false) {
            return '';
        }
        $indicators = [];
        for ($i = 0, $count = count($this->items); $i < $count; $i++) {
            $options = ['data-target' => '#' . $this->options['id'], 'data-slide-to' => $i];
            if ($i === 0) {
                Html::addCssClass($options, 'banner__nav--active');
            }
            $indicators[] = Html::tag('li', '', $options);
        }

        return Html::tag('ul', '', ['class' => 'banner__nav banner__nav--news']);
//        return Html::tag('ol', implode("\n", $indicators), ['class' => 'banner__nav banner__nav--news']);
    }

    /**
     * Renders previous and next control buttons.
     * @throws InvalidConfigException if [[controls]] is invalid.
     */
    public function renderControls()
    {
        if (isset($this->controls[0], $this->controls[1])) {
            return $this->controls[0] . "\n" . $this->controls[1];
        } elseif ($this->controls === false) {
            return '';
        } else {
            throw new InvalidConfigException('The "controls" property must be either false or an array of two elements.');
        }
    }

    /**
     * Renders a single carousel item
     * @param string|array $item a single item from [[items]]
     * @param integer $index the item index as the first item should be set to `active`
     * @return string the rendering result
     * @throws InvalidConfigException if the item is invalid
     */
    public function renderItem($item, $index)
    {
        if (is_string($item)) {
            $content = $item;
            $caption = null;
            $options = [];
        } elseif (isset($item['content'])) {
//            $content = $item['content'];
            $content = '';
            $caption = ArrayHelper::getValue($item, 'caption');
            if ($caption !== null) {
                $caption = Html::tag('div', $caption, ['class' => 'banner__slide-desc']);
            }
            $options = ArrayHelper::getValue($item, 'options', []);
        } else {
            throw new InvalidConfigException('The "content" option is required.');
        }

//        Html::addCssClass($options, ['widget' => 'item']);
        if (!empty($item['img'])) {
            Html::addCssStyle($options, ['background' => "url({$item['img']}) 00 no-repeat;"]);
        }
        if ($index === 0) {
            Html::addCssClass($options, 'banner__slide--active');
        }
        return Html::tag('div', $content . "\n" . $caption, $options);
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
//        $this->registerPlugin('carousel');
        $content = '';
        if (!empty($this->items)) {
            $content = implode("\n", [
                $this->prefix,
                $this->renderIndicators(),
                $this->renderItems(),
                $this->renderControls()
            ]);
        }
        return Html::tag('div', $content, $this->options);
    }

}
