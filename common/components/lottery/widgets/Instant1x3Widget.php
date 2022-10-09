<?php

namespace common\components\lottery\widgets;

use yii\base\Widget;
use common\components\lottery\widgets\models\BetForm;

/**
 * LotteryWidget
 *
 * @author fistashkin
 */
class Instant1x3Widget extends Widget
{

    public $numbers = 3;
    public $defaultItemClass = 'btn-default';
    public $selectedItemClass = 'btn-success';
    public $minNumbers = 1;
    public $maxNumbers = 1;
    public $betDefault = null;
    public $betMax = null;
    public $betNumberMax = 3;
    public $gameName;

    public function init()
    {
        switch ($this->numbers) {
            case 3:
                $this->minNumbers = 1;
                $this->maxNumbers = 1;
//                $this->rows = 4;
                break;
            case 9:
                $this->minNumbers = 3;
                $this->maxNumbers = 3;
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

        return $this->render('instantField', [
                    'count' => $this->numbers,
                    'defaultClass' => $this->defaultItemClass,
                    'selectedClass' => $this->selectedItemClass,
                    'minNumbers' => $this->minNumbers,
                    'maxNumbers' => $this->maxNumbers,
                    'gameName' => $this->gameName,
                    'model' => $model,
                    'betDefault' => $this->betDefault,
                    'betNumberMax' => $this->betNumberMax,
                    'betMax' => $this->betMax,
                        //TODO check funds on client
                        ]
        );
    }

}
