<?php

namespace common\components\lottery\models;

use Yii;
use yii\helpers\Url;

/**
 * Instant Lottery 1 out of 3 model
 *
 * @author Mega
 */
class L1x3 extends BaseLottery
{

    const ID          = '1x3';
    const NAME        = '«1 из 3»';
    const DRAW_CONFIG = [
        'count' => 1,
        'max'   => 3,
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
