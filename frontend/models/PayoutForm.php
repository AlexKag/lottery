<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class PayoutForm extends Model
{


    const PAYMENT_PERFECTMONEY = 'pm';
    const PAYMENT_PAYEER = 'payeer';
    const PAYMENT_BITCOIN = 'bitcoin';
    const PAYMENT_ETHEREUM = 'ethereum';

    public static $methods = [
        self::PAYMENT_PERFECTMONEY => 'Perfect Money',
        self::PAYMENT_PAYEER => 'Payeer',
//        self::PAYMENT_BITCOIN => 'Bitcoin',
//        self::PAYMENT_ETHEREUM => 'Ethereum',
    ];
    public $method = self::PAYMENT_PERFECTMONEY;
    public $amount = 0;
    public $units = 'USD';
    public $amountMax = 1e10;
    public $toAccount; //На какой счёт переводить деньги

    public function rules()
    {
        return [
            [['method', 'amount', 'toAccount'], 'required'],
            ['method', 'in', 'range' => array_keys(self::$methods)],
            ['amount', 'double', 'min' => (YII_ENV == 'dev' ? 0.01 : 1), 'max' => $this->amountMax],
            ['toAccount', 'match', 'pattern' => '/^[UEGBP]\d+$/'], //Perfect Money + Payeer
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
            'toAccount' => Yii::t('app', 'На счёт'),
        ];
    }

}
