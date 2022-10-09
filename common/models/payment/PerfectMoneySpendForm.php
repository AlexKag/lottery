<?php

namespace common\models\payment;

use Yii;
use yii\base\Model;

/**
 * This is the model class for Perfect Money spend form.
 *
 * @property integer $AccountID
 * @property string $PassPhrase
 * @property string $Payer_Account
 * @property string $Payee_Account
 * @property string $Amount
 * @property string $Memo
 * @property string $PAYMENT_ID
 * @property string $code
 * @property integer $Period
 */
class PerfectMoneySpendForm extends Model {

    CONST URL = 'https://perfectmoney.is/acct/confirm.asp';

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['AccountID', 'path'], 'required'],
            [['AccountID', 'Period'], 'integer'],
            [['PassPhrase', 'Amount', 'Memo', 'PAYMENT_ID', 'code'], 'string', 'max' => 255],
            [['Payer_Account', 'Payee_Account'], 'string', 'max' => 15]
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
