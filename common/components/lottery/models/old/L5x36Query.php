<?php

namespace common\components\lottery\models;

/**
 * This is the ActiveQuery class for [[L5x36]].
 *
 * @see L5x36
 */
class L5x36Query extends \yii\db\ActiveQuery
{
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return L5x36[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return L5x36|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
