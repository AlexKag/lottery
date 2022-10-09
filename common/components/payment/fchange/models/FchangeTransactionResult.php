<?php

namespace common\components\payment\fchange\models;

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
