<?php

namespace common\components\lottery\widgets\models;

use Yii;
use yii\base\Model;
use yii\helpers\Json;
use common\validators\JsonValidator;

/**
 * ContactForm is the model behind the contact form.
 */
class BetForm extends Model
{

    public $bet;
    public $paid;
    public $multidraw_count;
    //Ограничение ставки
    public static $paidLimit = 150;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        if (Yii::$app->user->isGuest) {
            $compareMessage   = 'Пожалуйста, войдите на сайт или зарегистрируйтесь!';
            $comparePaidLimit = 1;
        } elseif (Yii::$app->user->identity->userProfile->paidLimit > Yii::$app->user->identity->userProfile->account) {
            $comparePaidLimit = Yii::$app->user->identity->userProfile->account;
            $paidLimit        = Yii::$app->formatter->asCurrency($comparePaidLimit);
            $compareMessage   = "Недостаточно средств. Сумма на счету $paidLimit. Пожалуйста, пополните счёт, чтобы снять ограничения.";
        } else {
            $comparePaidLimit = Yii::$app->user->identity->userProfile->paidLimit;
            $paidLimit        = Yii::$app->formatter->asCurrency($comparePaidLimit, null, [
                \NumberFormatter::MIN_FRACTION_DIGITS => 0,
                \NumberFormatter::MAX_FRACTION_DIGITS => 0,
            ]);
            $compareMessage   = "Превышен лимит на оплату $paidLimit.";
//            $compareMessage = "Превышен лимит на оплату $paidLimit. Пройдите валидацию, чтобы снять ограничения.";
        }
        return [
//            [['bet'], 'required', 'whenClient' => 'function(attribute,value){return 0;}'],
            [['bet'], 'required', 'message' => 'Выберите числа или нажмите кнопку "Случайная ставка".'],
//            [['bet'], 'required', 'enableClientValidation' => false],
            [['bet'], JsonValidator::className(), 'message' => 'Сделайте ставку!'], //FIXME проверка при неполной ставке на клиенте и сервере
            [['bet', 'draws_count'], 'filter', 'filter' => 'strip_tags'],
            ['paid', 'compare', 'compareValue' => ceil($comparePaidLimit), 'operator' => '<=', 'message' => $compareMessage],
            ['paid', 'compare', 'compareValue' => ceil(self::$paidLimit), 'operator' => '<=', 'message' => 'Превышена максимально возможная сумма ставки.'],
            ['multidraw_count', 'integer', 'min' => 0, 'max' => Yii::$app->keyStorage->get('lottery.common.multidraw_count') - 1],
            ['multidraw_count', 'default', 'value' => 1],
            ['paid', 'number', 'min' => 1, 'message' => 'Сделайте ставку'],
//            ['multidraw_count', 'compare', 'compareValue' => Yii::$app->keyStorage->get('lottery.common.multidraw_count'), 'operator' => '<=']
        ];
    }

    public function getBets()
    {
        return Json::decode($this->bet);
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        $tmp            = Yii::$app->formatter->asCurrency(0);
        $currencySymbol = substr($tmp, -1);
        return [
//            'lottery_id' => Yii::t('frontend', 'Номер тиража'),
            'multidraw_count' => Yii::t('frontend', 'Количество тиражей'),
            'paid' => Yii::t('frontend', 'Ваша ставка, ' . $currencySymbol),
            'bet' => Yii::t('frontend', 'Ставка'),
        ];
    }

}
