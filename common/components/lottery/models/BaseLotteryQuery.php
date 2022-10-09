<?php

namespace common\components\lottery\models;

use yii\db\ActiveQuery;

class BaseLotteryQuery extends ActiveQuery
{

    public function enabled($state = true)
    {
        return $this->andWhere(['enabled' => $state]);
    }

}
