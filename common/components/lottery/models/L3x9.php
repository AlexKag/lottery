<?php

namespace common\components\lottery\models;

use Yii;
use yii\helpers\Url;

/**
 * Instant Lottery 1 out of 3 model
 *
 * @author Mega
 */
class L3x9 extends BaseLottery
{

    const ID          = '3x9';
    const NAME        = '«3 из 9»';
    const DRAW_CONFIG = [
        'count' => 3,
        'max'   => 9,
    ];

    protected static function _ceilToDraw($timestamp)
    {
        return time() + 10;
    }

    public function get_Superprize()
    {
        return null;
    }

}
