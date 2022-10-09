<?php

namespace common\components\lottery\widgets;

use yii\base\Widget;

/**
 * LotteryWidget
 *
 * @author fistashkin
 */
class LotteryCarouselWidget extends Widget {

    public $itemsOnSlide = 4;
    public $items = [];
    protected $itemDefault = [
        'name' => '6 из 45',
        'logo_url' => '/img/lotto_2.png',
        'countdown_timestamp' => 3600,
        'superprize' => 1000000,
        'game_url' => '/lottery6x45/index',
    ];

//    public function init() {
//        
//    }

    public function run() {

        return $this->render('raffleCarousel', [
                    'js' => $this->_createCountdown($this->items),
                    'items' => $this->items,
                    'itemsOnSlide' => $this->itemsOnSlide,
                        ]
        );
    }

    const COUNTDOWN_JS = <<<JS
$("#raffle__count-%d").countdown(cdDate(%s), function (event) {
    $(this).html(event.strftime("<span>%%H</span> ч <span>%%M</span> м <span>%%S</span> с"));

});
JS;

    protected function _createCountdown(array $items) {
        $js = <<<JS
function cdDate(delta){
    var daysLeft = parseInt(delta/86400);
    delta -= 86400 * daysLeft;

    var hoursLeft = parseInt(delta/3600);
    delta -= 3600 * hoursLeft;
                
    var minutesLeft = parseInt(delta/60);
    delta -= 60 * minutesLeft;
                
    var secondsLeft = parseInt(delta);
                
    thisDate  = new Date();
    nDate = new Date(thisDate.getFullYear(), thisDate.getMonth() + 1, thisDate.getDay() + daysLeft, thisDate.getHours() + hoursLeft, thisDate.getMinutes() + minutesLeft, thisDate.getSeconds() + secondsLeft);
    
    return nDate;
}
JS;
        foreach ($items as $key => $item) {
            if (isset($item['countdown_timestamp']) && is_numeric($item['countdown_timestamp'])) {
//                $datetime = strftime('%Y/%m/%d %H:%M:00', $item['countdown_timestamp']);
                $deltatime = $item['countdown_timestamp'] - time();
                $js .= sprintf(static::COUNTDOWN_JS, $key, $deltatime);
            }
        }
        return $js;
    }

}
