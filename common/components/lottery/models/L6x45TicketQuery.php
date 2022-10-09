<?php

namespace common\components\lottery\models;

/**
 * This is the ActiveQuery class for [[L6x45Ticket]].
 *
 * @see L6x45Ticket
 */
class L6x45TicketQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return L6x45Ticket[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return L6x45Ticket|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
