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
class PerfectMoneyTransaction extends Model {

    public $PAYEE_ACCOUNT;
    public $PAYMENT_ID;
    public $PAYMENT_AMOUNT;
    public $PAYMENT_UNITS;
    public $PAYMENT_BATCH_NUM;
    public $PAYER_ACCOUNT;
    public $TIMESTAMPGMT;
    public $V2_HASH;
    public $AlternateMerchantPassphrase;
    public $SUGGESTED_MEMO;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['PAYEE_ACCOUNT', 'PAYMENT_ID', 'PAYMENT_AMOUNT', 'PAYMENT_UNITS', 'PAYMENT_BATCH_NUM', 'PAYER_ACCOUNT', 'TIMESTAMPGMT'], 'required'],
            [['PAYMENT_AMOUNT'], 'double', 'min' => 0.01],
            [['PAYMENT_UNITS'], 'string', 'max' => 3],
            [['SUGGESTED_MEMO'], 'string'],
            [['PAYEE_ACCOUNT', 'PAYER_ACCOUNT'], 'string', 'max' => 10],
            [['PAYMENT_BATCH_NUM', 'TIMESTAMPGMT'], 'integer', 'min' => 1],
            [['PAYMENT_ID',], 'string', 'max' => 100],
//            [['V2_HASH'], 'string', 'max' => 32],
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

    public function getHash() {
//        PAYMENT_ID:PAYEE_ACCOUNT:PAYMENT_AMOUNT:PAYMENT_UNITS:PAYMENT_BATCH_NUM:PAYER_ACCOUNT:AlternateMerchantPassphraseHash:TIMESTAMPGMT
        $str = implode(':', [
            $this->PAYMENT_ID,
            $this->PAYEE_ACCOUNT,
            $this->PAYMENT_AMOUNT,
            $this->PAYMENT_UNITS,
            $this->PAYMENT_BATCH_NUM,
            $this->PAYER_ACCOUNT,
            $this->AlternateMerchantPassphraseHash,
            $this->TIMESTAMPGMT
        ]);
        return strtoupper(md5(strtouppper($str)));
    }

    public function get_PAYMENT_ID() {
        return empty($this->PAYMENT_ID) ? 'NULL' : $this->PAYMENT_ID;
    }

    public function set_PAYMENT_ID($value) {
        $this->PAYMENT_ID = $value;
    }

    public function getAlternateMerchantPassphraseHash() {
        return strtoupper(md5($this->AlternateMerchantPassphrase));
    }

}
