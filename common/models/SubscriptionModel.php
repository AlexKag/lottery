<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%subscription}}".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $email
 * @property integer $type_common
 * @property integer $type_start
 * @property integer $created_at
 * @property integer $updated_at
 */
class SubscriptionModel extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscription}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            [['user_id', 'type_common', 'type_start', 'created_at', 'updated_at'], 'integer'],
            [['email'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'email' => Yii::t('common', 'Email'),
            'type_common' => Yii::t('common', 'Type Common'),
            'type_start' => Yii::t('common', 'Type Start'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\SubscriptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\SubscriptionQuery(get_called_class());
    }

}
