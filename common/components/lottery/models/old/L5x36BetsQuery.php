<?php

namespace common\components\lottery\models;

/**
 * This is the ActiveQuery class for [[L5x36Bets]].
 *
 * @see L5x36Bets
 */
class L5x36BetsQuery extends \yii\db\ActiveQuery
{
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return L5x36Bets[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return L5x36Bets|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Призовой фонд
     * @param int $lottery_id
     * @return null|int
     */
    public function pool($lottery_id)
    {
        return $this->andWhere(['lottery_id' => $lottery_id])->sum('[[paid]]');
    }

}
