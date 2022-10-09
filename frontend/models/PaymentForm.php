<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class PaymentForm extends Model
{

    const PAYMENT_PERFECTMONEY = 'pm';
    const PAYMENT_PAYEER = 'payeer';
    const PAYMENT_BITCOIN = 'bitcoin';
    const PAYMENT_FCHANGE = 'fchange';
    const PAYMENT_FCHANGE_QWRUB = 'QWRUB';
    const PAYMENT_FCHANGE_YAMRUB = 'YAMRUB';
    const PAYMENT_FCHANGE_OKUSD = 'OKUSD';
    const PAYMENT_FCHANGE_ADVCUSD = 'ADVCUSD';
    const PAYMENT_FCHANGE_CARDRUB = 'CARDRUB';

    public static $methods = [
        self::PAYMENT_PERFECTMONEY => 'Perfect Money',
        self::PAYMENT_PAYEER => 'Payeer',
//        self::PAYMENT_FCHANGE_QWRUB => 'QIWI RUB',
//        self::PAYMENT_FCHANGE_YAMRUB => 'Яндекс деньги',
//        self::PAYMENT_FCHANGE_OKUSD => 'OkPay USD',
//        self::PAYMENT_FCHANGE_ADVCUSD => 'AdvCash USD',
//        self::PAYMENT_FCHANGE_CARDRUB => 'Visa/Master RUB',
    ];
    public $method = self::PAYMENT_PERFECTMONEY;
    public $amount = (YII_ENV == 'dev' ? 0.01 : 0);
    public $units = 'USD';
    public $amountMax = 1e10;
    public $token = '';

    public function rules()
    {
        return [
            [['method', 'amount'], 'required'],
            ['method', 'in', 'range' => array_keys(self::$methods)],
            ['amount', 'double', 'min' => (YII_ENV == 'dev' ? 0.01 : 1), 'max' => $this->amountMax],
            ['token', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'method' => Yii::t('app', 'Сервис'),
            'amount' => Yii::t('app', 'Сумма'),
        ];
    }

}
