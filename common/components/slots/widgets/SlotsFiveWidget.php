<?php

namespace common\components\slots\widgets;

use yii\base\Widget;
use common\components\slots\widgets\models\FiveBetForm as BetForm;

/**
 * SlotsFiveWidget
 *
 * @author fistashkin
 */
class SlotsFiveWidget extends Widget
{

    public $numbers = 3;
    public $fields  = [];
//    public $defaultItemClass  = 'btn-default';
//    public $selectedItemClass = 'btn-success';
//    public $minNumbers        = 6;
//    public $maxNumbers        = 13;
//    public $betNumberMax        = 13;
//    public $betDefault        = null;
//    public $pricing           = [
//        6  => 1,
//        7  => 7,
//        8  => 28,
//        9  => 84,
//        10 => 210,
//        11 => 462,
//        12 => 924,
//        13 => 1716
//    ];
//    public $lottery;
    public $gameName;

    public function init()
    {
        
    }

    public function run()
    {
        $model = new BetForm();

        return $this->render('fieldFive', [
                    'fields' => $this->fields,
//                    'defaultClass'  => $this->defaultItemClass,
//                    'selectedClass' => $this->selectedItemClass,
//                    'minNumbers'    => $this->minNumbers,
//                    'maxNumbers'    => $this->maxNumbers,
                    'model' => $model,
//                    'lottery'       => $this->lottery,
//                    'pricing'       => $this->pricing,
//                    'betNumberMax'  => $this->betNumberMax,
//                    'betDefault'    => $this->betDefault,
                    'gameName' => $this->gameName,
                        //TODO check funds on client
                        ]
        );
    }

}
