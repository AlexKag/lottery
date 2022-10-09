<?php

namespace common\components\lottery\models;

/**
 * This is the ActiveQuery class for [[Instant1x3Ticket]].
 *
 * @see Instant1x3Ticket
 */
class Instant1x3TicketQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Instant1x3Ticket[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Instant1x3Ticket|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
