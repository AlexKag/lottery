<?php

namespace common\models\payment;

use Yii;
use yii\base\Model;

/**
 * This is the model class for F-Change Payment Transaction Form
 * sent directly to the merchant’s system by the F-Change server.
 *
 * @property string PAYEE_ACCOUNT
 * @property string PAYMENT_ID
 * @property string PAYMENT_AMOUNT
 * @property string PAYMENT_UNITS
 * @property string PAYMENT_BATCH_NUM
 * @property string PAYER_ACCOUNT
 * @property string TIMESTAMPGMT
 * @property string V2_HASH
 */
//    [
//    'm_operation_id' => '230653993'
//    'm_operation_ps' => '2609'
//    'm_operation_date' => '08.09.2016 07:49:29'
//    'm_operation_pay_date' => '08.09.2016 07:49:37'
//    'm_shop' => '230590528'
//    'm_orderid' => '18-1473310099'
//    'm_amount' => '0.01'
//    'm_curr' => 'USD'
//    'm_desc' => '0J/QvtC/0L7Qu9C90LXQvdC40LUg0YHRh9GR0YLQsCBGcmVlZG9tIExvdHRvINC/0L7Qu9GM0LfQvtCy0LDRgtC10LvRjyDQktC40YTQsNC70LjQuSA='
//    'm_status' => 'success'
//    'm_sign' => '1251C961FA97982C2F773AF9144C62E60BEA2733D7E09548261E82391972B589'
//    'lang' => 'ru'
//]
class FchangeTransactionResult extends Model
{

    public $PAYEE_ACCOUNT;
    public $PAYMENT_ID;
    public $PAYMENT_AMOUNT;
    public $PAYMENT_UNITS;
    public $PAYMENT_BATCH_NUM;
//    public $PAYER_ACCOUNT;
    public $TIMESTAMPGMT;
    public $SUGGESTED_MEMO;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PAYEE_ACCOUNT', 'PAYMENT_ID', 'PAYMENT_AMOUNT', 'PAYMENT_UNITS', 'PAYMENT_BATCH_NUM', 'TIMESTAMPGMT'], 'required'],
            [['PAYMENT_AMOUNT'], 'double', 'min' => 0.01],
            [['PAYMENT_UNITS'], 'string', 'max' => 3],
            [['SUGGESTED_MEMO'], 'string'],
            [['PAYEE_ACCOUNT'], 'string', 'max' => 10],
            [['PAYMENT_BATCH_NUM', 'TIMESTAMPGMT'], 'integer', 'min' => 1],
            [['PAYMENT_ID',], 'string', 'max' => 100],
//            [['V2_HASH'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'PAYMENT_AMOUNT' => Yii::t('common', 'Сумма'),
        ];
    }

}
