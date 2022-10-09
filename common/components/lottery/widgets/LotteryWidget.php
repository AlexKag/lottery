<?php

namespace common\components\lottery\widgets;

use yii\base\Widget;
use common\components\lottery\widgets\models\BetForm;

/**
 * LotteryWidget
 *
 * @author fistashkin
 */
class LotteryWidget extends Widget
{

    public $numbers           = 45;
    public $columns           = 9;
    public $defaultItemClass  = 'btn-default';
    public $selectedItemClass = 'btn-success';
    public $minNumbers        = 6;
    public $maxNumbers        = 13;
    public $betNumberMax        = 13;
    public $betDefault        = null;
    public $pricing           = [
        6  => 1,
        7  => 7,
        8  => 28,
        9  => 84,
        10 => 210,
        11 => 462,
        12 => 924,
        13 => 1716
    ];
    public $lottery;

    public function init()
    {
        switch ($this->numbers) {
            case 36:
                $this->minNumbers = 5;
                $this->maxNumbers = 12;
//                $this->rows = 4;
                break;
            case 45:
                $this->minNumbers = 6;
                $this->maxNumbers = 13;
                $this->betNumberMax = 45;
//                $this->rows = 5;
                break;
            default:
//                $this->rows = 5;
                break;
        }
    }

    public function run()
    {
        $model = new BetForm();
        
        return $this->render('field', [
                    'count'         => $this->numbers,
                    'defaultClass'  => $this->defaultItemClass,
                    'selectedClass' => $this->selectedItemClass,
                    'minNumbers'    => $this->minNumbers,
                    'maxNumbers'    => $this->maxNumbers,
                    'model'         => $model,
                    'lottery'       => $this->lottery,
                    'pricing'       => $this->pricing,
                    'betNumberMax'  => $this->betNumberMax,
                    'betDefault'    => $this->betDefault,
            //TODO check funds on client
                        ]
        );
    }

}
