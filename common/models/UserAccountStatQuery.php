<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserAccountStat]].
 *
 * @see UserAccountStat
 */
class UserAccountStatQuery extends \yii\db\ActiveQuery
{
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return UserAccountStat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserAccountStat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
