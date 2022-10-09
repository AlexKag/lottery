<?php

namespace common\models\payment;

use Yii;
use yii\base\Model;

/**
 * This is the model class for Perfect Money Payment Transaction Form 
 * sent directly to the merchant’s system by the Perfect Money® server.
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
class PerfectMoneyReturn extends Model {

    public $PAYEE_ACCOUNT;
    public $PAYMENT_AMOUNT;
    public $PAYMENT_UNITS;
    public $PAYMENT_BATCH_NUM;
    public $PAYER_ACCOUNT;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['PAYEE_ACCOUNT', 'PAYMENT_AMOUNT', 'PAYMENT_UNITS', 'PAYMENT_BATCH_NUM'], 'required'],
            ['PAYER_ACCOUNT', 'safe'],
            [['PAYEE_ACCOUNT', 'PAYER_ACCOUNT'], 'string', 'max' => 10],
            [['PAYMENT_AMOUNT'], 'double', 'min' => 0.01],
            [['PAYMENT_UNITS'], 'string', 'max' => 3],
            [['PAYMENT_BATCH_NUM'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'PAYMENT_AMOUNT' => Yii::t('common', 'Сумма'),
        ];
    }

}
