<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * CheckTicket
 */
class LCheckTicketForm extends Model
{

//    public $lottery_id;
//    public $lottery_type;
    public $ticket_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
//            [['lottery_type', 'ticket_id'], 'required'],
            [['ticket_id'], 'required'],
            [['ticket_id'], 'integer'],
//            ['lottery_type', 'in', 'range' => array_keys(Yii::$app->params['availableLotteries'])]
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
//            'lottery_id' => Yii::t('frontend', 'Номер тиража'),
            'ticket_id'  => Yii::t('frontend', 'Номер билета'),
        ];
    }

}
