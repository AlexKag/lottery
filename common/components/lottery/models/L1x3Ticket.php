<?php

namespace common\components\lottery\models;

use yii\web\BadRequestHttpException;

/**
 * Instant Lottery 1 out of 3 Ticket
 *
 * @author Mega
 */
class L1x3Ticket extends BaseTicket
{

    const ID          = '1x3';
    const MIN_NUMBERS = 1;
    const MAX_NUMBERS = 1;
    const NAME        = '«1 из 3»';

//    public function rules()
//    {
//        $rules[] = ['lottery_id', 'default', 'value' => 0];
//        $rules = array_merge($rules, parent::rules());
//        return $rules;
//    }

    /**
     * @inheritdoc
     */
//    public static function tableName()
//    {
//        return '{{%l' . static::ID . '_ticket}}';
//    }

    public function calcPaid(array $bet)
    {
//        Yii::$app->getSession()->setFlash('alert', [
//                    'body'    => \Yii::t('frontend', 'Недостаточно средств на счёту.'),
//                    'options' => ['class' => 'alert-danger']
//                ]);
        throw new BadRequestHttpException('Can\'t calculate paid value');
        return 0;
    }

//    public function getLottery()
//    {
//        $class = __NAMESPACE__ . '\\L' . static::ID;
//        return $this->hasOne($class, [ 'id' => 'lottery_id']);
////        return $this->hasOne(L6x45::className(), [ 'lottery_id' => 'id']);
//    }

}
