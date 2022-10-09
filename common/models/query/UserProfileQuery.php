<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class UserTokenQuery
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserProfileQuery extends ActiveQuery
{

    /**
     * @param $type
     * @return $this
     */
    public function byTelegramId($id)
    {
        $this->andWhere(['telegram_id' => $id]);
        return $this;
    }

    /**
     * @param $token
     * @return $this
     */
    public function byToken($token)
    {
        $this->andWhere(['token' => $token]);
        return $this;
    }
}