<?php

namespace common\models\payment;

use Yii;
use yii\base\Model;

/**
 * This is the model class for Perfect Money spend form.
 *
 * @property string PAYEE_ACCOUNT
 * @property string PAYEE_NAME
 * @property string PAYMENT_AMOUNT
 * @property string PAYMENT_UNITS
 * @property string PAYMENT_ID
 * @property string STATUS_URL
 * @property string PAYMENT_URL
 * @property string PAYMENT_URL_METHOD
 * @property string NOPAYMENT_URL
 * @property string NOPAYMENT_URL_METHOD
 * @property string BAGGAGE_FIELDS
 * @property string SUGGESTED_MEMO
 * @property string SUGGESTED_MEMO_NOCHANGE
 * @property string FORCED_PAYER_ACCOUNT
 * @property string AVAILABLE_PAYMENT_METHODS
 * @property string DEFAULT_PAYMENT_METHOD
 * @property string FORCED_PAYMENT_METHOD
 * @property string INTERFACE_LANGUAGE
 */
class PerfectMoneyStep1 extends Model {

    CONST URL = 'https://perfectmoney.is/api/step1.asp';

    public $PAYEE_ACCOUNT;
    public $PAYEE_NAME;
    public $PAYMENT_AMOUNT;
    public $PAYMENT_UNITS = 'USD';
    public $PAYMENT_ID;
    public $STATUS_URL;
    public $PAYMENT_URL;
    public $PAYMENT_URL_METHOD = 'POST';
    public $NOPAYMENT_URL;
    public $NOPAYMENT_URL_METHOD = 'POST';
    public $BAGGAGE_FIELDS = '';
    public $SUGGESTED_MEMO = null;
    public $SUGGESTED_MEMO_NOCHANGE = null;
    public $FORCED_PAYER_ACCOUNT = null;
    public $AVAILABLE_PAYMENT_METHODS = 'all';
    public $DEFAULT_PAYMENT_METHOD = null;
    public $FORCED_PAYMENT_METHOD = null;
    public $INTERFACE_LANGUAGE = 'ru-RU';

    public function init() {
        parent::init();
        $this->INTERFACE_LANGUAGE = Yii::$app->language;
        $this->PAYEE_NAME = Yii::$app->name;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['PAYEE_ACCOUNT', 'PAYEE_NAME', 'PAYMENT_AMOUNT', 'PAYMENT_UNITS', 'PAYMENT_URL', 'NOPAYMENT_URL'], 'required'],
//            [['AccountID', 'Period'], 'integer'],
            [['PAYMENT_AMOUNT'], 'double', 'min' => 0.01],
            [['PAYMENT_UNITS'], 'string', 'max' => 3],
            [['PAYMENT_URL_METHOD', 'NOPAYMENT_URL_METHOD'], 'string', 'max' => 4],
            [['AVAILABLE_PAYMENT_METHODS', 'DEFAULT_PAYMENT_METHOD', 'FORCED_PAYMENT_METHOD', 'INTERFACE_LANGUAGE', 'FORCED_PAYER_ACCOUNT'], 'string', 'max' => 10],
            [[
            'PAYEE_NAME',
            'PAYMENT_ID',
            'BAGGAGE_FIELDS',
            'SUGGESTED_MEMO',
                ], 'string', 'max' => 100],
            [['PAYEE_ACCOUNT'], 'string', 'max' => 15],
            [[
            'STATUS_URL',
            'PAYMENT_URL',
            'NOPAYMENT_URL'
                ], 'url'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('common', 'ID'),
        ];
    }

}
