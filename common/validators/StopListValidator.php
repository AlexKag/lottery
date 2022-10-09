<?php

namespace common\validators;

use yii\validators\Validator;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\StopList;

/**
 * @author Alex K
 */
class StopListValidator extends Validator
{

    public $stoplist = [];
    public $stoplistType = StopList::TYPE_EMAIL;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('common', '"{attribute}" is not allowed.');
        }
//        if (empty($this->stoplist)) {
//            $this->stoplist = StopList::findAll([
//                        'type' => $this->stoplistType,
//            ]);
//            $this->stoplist = ArrayHelper::map($this->stoplist, 'id', 'value');
//        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (StopList::isInStopList($model->$attribute, [$this->stoplistType])) {
                $this->addError($model, $attribute, $this->message);
        }
//        foreach($this->stoplist as $expr) {
//            if (preg_match("/$expr/", $model->$attribute)) {
//                $this->addError($model, $attribute, $this->message);
//                break;
//            }
//        }
    }

}
