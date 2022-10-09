<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "{{%black_list}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $value
 * @property string $is_regexp
 * @property integer $created_at
 * @property integer $updated_at
 */
class StopList extends \yii\db\ActiveRecord {

    const TYPE_EMAIL = 'email';
    const TYPE_WORD = 'word';

    public static $types = [
        self::TYPE_EMAIL => self::TYPE_EMAIL,
        self::TYPE_WORD => self::TYPE_WORD,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%stop_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type', 'value'], 'required'],
            ['is_regexp', 'default', 'value' => false],
            [['type'], 'in', 'range' => self::$types],
            [['value'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 50],
            [['enabled', 'is_regexp'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ID'),
            'type' => Yii::t('backend', 'Type'),
            'value' => Yii::t('backend', 'Value'),
            'enabled' => Yii::t('backend', 'Enabled'),
            'is_regexp' => Yii::t('backend', 'PCRE regular expression'),
            'description' => Yii::t('backend', 'Item Description'),
            'created_at' => Yii::t('backend', 'Created At'),
            'updated_at' => Yii::t('backend', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return Instant1x3TicketQuery the active query used by this AR class.
     */
//    public static function find() {
//        return new StopListQuery(get_called_class());
//    }

    /**
     * Поиск значения по всему стоп листу
     * @param string $searchString
     * @param array $types
     * @return boolean
     */
    public static function isInStopList($searchString, array $types = [self::TYPE_EMAIL]) {
        foreach ($types as $type) {
            if (!in_array($type, static::$types)) {
                throwException('Unknown Stop List type');
            }
            $stoplist = static::find()
                    ->where(['type' => $type])
                    ->all();
            foreach ($stoplist as $item) {
                if($item->isMatch($searchString)){
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Проверка соответствия значения выражению из стоп листа
     * @param string $value
     * @return boolean
     */
    public function isMatch($value){
        if($this->is_regexp){
            return preg_match("/{$this->value}/", $value);
        }
        $res = strpos($value, $this->value);
        return !($res === false);
    }

}
