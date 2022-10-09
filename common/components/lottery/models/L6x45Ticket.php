<?php

namespace common\components\lottery\models;

//use Yii;
//use yii\helpers\Json;
//use L6x45Draw;

/**
 * L6x45Ticket
 *
 * @author Mega
 */
class L6x45Ticket extends BaseTicket
{

    const ID          = '6x45';
    const MIN_NUMBERS = 6;
    const MAX_NUMBERS = 13;
    const NAME        = '«6 из 45»';

    /**
     * @inheritdoc
     */
//    public static function tableName()
//    {
//        return '{{%l'.static::ID.'_ticket}}';
//    }

    public function init()
    {
//        $vr = Yii::$app->keyStorage->get('lottery.'.static::ID.'.pricing');
//        $this->pricing = Json::decode(Yii::$app->keyStorage->get('lottery.'.static::ID.'.pricing'));
        parent::init();
        $this->bet_cnt         = count($this->_bet);
        $this->is_bet_extended = $this->bet_cnt > static::MIN_NUMBERS;
    }

//    public function getDraw()
//    {
//        return $this->hasMany(L6x45Draw::className(), [ 'lottery_id' => 'id'])->inverseOf('ticket');
//    }
//    public function getLottery()
//    {
//        return $this->hasOne(L6x45::className(), [ 'lottery_id' => 'id']);
//    }
}
