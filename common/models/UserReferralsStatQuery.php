<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserReferralsStat]].
 *
 * @see UserReferralsStat
 */
class UserReferralsStatQuery extends \yii\db\ActiveQuery
{
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return UserReferralsStat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserReferralsStat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Query user referral stats
     * @param integer $id
     * @return array|null
     */
    public function userStat($id)
    {

    }

    /**
     * Query user referral count
     * @param integer $userId
     * @param integer $refLevel
     * @return array|null
     */
    public function userRefsCount($userId, $refLevel = 1)
    {
        
    }

}
