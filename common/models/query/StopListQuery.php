<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[Instant1x3Ticket]].
 *
 * @see Instant1x3Ticket
 */
class StopListQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return StopList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return StopList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
