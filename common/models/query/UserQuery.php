<?php

namespace common\models\query;

use Yii;
use common\models\User;
use yii\db\ActiveQuery;

/**
 * Class UserQuery
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserQuery extends ActiveQuery
{

    /**
     * @return $this
     */
    public function notDeleted()
    {
        $this->andWhere(['!=', 'status', User::STATUS_DELETED]);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => User::STATUS_ACTIVE]);
        return $this;
    }

    //mysql manual p. 905 Date/Time Functions
//    SELECT id,username, FROM_UNIXTIME(created_at), EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(created_at)), CURDATE() FROM lottery.lottery_user group by EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(created_at))

        /**
     * Число рефералов
     * @param type $user_id
     * @param type $refLevel
     * @return integer
     */
        public function refCount($user_id, $refLevel = 1)
    {
        $table = User::tableName();
        $params = [
            ':user_id' => $user_id,
            ':status' => User::STATUS_ACTIVE,
        ];
        switch ($refLevel) {
            case 1:
                $sql = "SELECT count(DISTINCT b.id) AS cnt "
                    . "FROM $table as a "
                    . "INNER JOIN $table as b "
                    . "ON a.referral_id = b.referrer_id "
                    . "WHERE a.id = :user_id AND a.status = :status GROUP BY a.id";
                break;
            case 2:
                $sql = "SELECT count(DISTINCT c.id) AS cnt "
                    . "FROM $table as a "
                    . "INNER JOIN $table as b "
                    . "ON a.referral_id = b.referrer_id "
                    . "INNER JOIN $table as c "
                    . "ON b.referral_id = c.referrer_id "
                    . "WHERE a.id = :user_id AND a.status = :status GROUP BY a.id";
                break;
            case 3:
                $sql = "SELECT count(DISTINCT b.id) AS cnt "
                    . "FROM $table as a "
                    . "INNER JOIN $table as b "
                    . "ON a.referral_id = b.referrer_id "
                    . "INNER JOIN $table as c "
                    . "ON b.referral_id = c.referrer_id "
                    . "INNER JOIN $table as d "
                    . "ON c.referral_id = d.referrer_id "
                    . "WHERE a.id =:user_id AND a.status = :status GROUP BY a.id";
                break;
        }
//        SELECT count(DISTINCT b.id) AS cnt FROM `lottery_user` as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id where a.id = 3 group by a.id
        return Yii::$app->db->createCommand($sql)->bindValues($params);
    }
}
