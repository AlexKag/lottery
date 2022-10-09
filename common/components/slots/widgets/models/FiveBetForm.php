<?php

namespace common\components\slots\widgets\models;

use Yii;
use yii\base\Model;
use yii\helpers\Json;
use common\validators\JsonValidator;

/**
 * SlotsFiveBetForm.
 */
class FiveBetForm extends Model
{

    public $betPerLine              = 1;
    public $linesCount              = 1;
    public $denomination            = 1;
    //Ограничение ставки
    public static $betMax           = 150;
    public static $limits           = [
        'denomination' => ['min' => 0.1, 'max' => 5],
        'betPerLine' => ['min' => 1, 'max' => 10],
        'linesCount' => ['min' => 1, 'max' => 5],
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['betPerLine', 'number', 'min' => self::$limits['betPerLine']['min'], 'max' => self::$limits['betPerLine']['max']],
            ['linesCount', 'number', 'min' => self::$limits['linesCount']['min'], 'max' => self::$limits['linesCount']['max']],
            ['denomination', 'number', 'min' => self::$limits['denomination']['min'], 'max' => self::$limits['denomination']['max']],
        ];
    }

//    public function getBets()
//    {
//        return Json::decode($this->bet);
//    }

    /**
     * @return array customized attribute labels
     */
//    public function attributeLabels()
//    {
//        $tmp            = Yii::$app->formatter->asCurrency(0);
//        $currencySymbol = substr($tmp, -1);
//        return [
////            'lottery_id' => Yii::t('frontend', 'Номер тиража'),
//            'multidraw_count' => Yii::t('frontend', 'Количество тиражей'),
//            'paid' => Yii::t('frontend', 'Ваша ставка, ' . $currencySymbol),
//            'bet' => Yii::t('frontend', 'Ставка'),
//        ];
//    }

}
