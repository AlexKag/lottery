<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/5/14
 * Time: 10:46 AM
 */

namespace common\models\query;

use yii\db\ActiveQuery;

class TimelineEventGameQuery extends ActiveQuery
{
    public function today()
    {
        $this->andWhere(['>=', 'created_at', strtotime('today midnight')]);
        return $this;
    }
}
