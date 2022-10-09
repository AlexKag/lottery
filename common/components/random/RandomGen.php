<?php

namespace common\components\random;

use Yii;
use yii\base\Model;

/**
  Configure as
  $randomConfig = [
  'count'=>6,
  'max'=>45
  ];
  Yii::$app->random->attributes = $randomConfig;

  Get draw combination
  $res = Yii::$app->random->numbers
  [0] => 1
  [1] => 30
  [2] => 42
  [3] => 2
  [4] => 4
  [5] => 34
 *
 */
class RandomGen extends Model implements RandomInterface
{

    public $min   = 1;
    public $max   = 36;
    public $count = 5;

    public function rules()
    {
        return [
            [['count', 'min', 'max'], 'integer', 'min' => 1,],
//            ['min', 'integer', 'min' => 0,],
        ];
    }

    public function getNumber()
    {
        return (version_compare(PHP_VERSION, '7.0') >= 0) ? random_int($this->min, $this->max) : mt_rand($this->min, $this->max);
    }

    public function getNumbers()
    {
        $res = [];
        $i   = 1;
        while ($i <= $this->count) {
            $num = $this->getNumber();
            if (!in_array($num, $res)) {
                $res[] = $num;
                $i++;
            }
        }
        return $res;
    }

}
