<?php

namespace common\components\slots\models;

use Yii;
use common\components\lottery\models\BaseTicket;
use yii\web\BadRequestHttpException;
use common\components\slots\widgets\models\FiveBetForm;
use common\validators\JsonValidator;
use cheatsheet\Time;

/**
 * Slots Five
 *
 * @author Mega
 */
class SlotsFiveTicket extends BaseTicket
{

    const ID           = 'slots_five';
//    const MIN_NUMBERS = 3;
//    const MAX_NUMBERS = 3;
    const NAME         = '«Пятерочка»';
    const DRAW_CONFIG  = [
        'count' => 5,
        'max' => 10,
    ];
    const LINES        = [
        1 => 'slot-prev',
        2 => 'slot-main', //center line
        3 => 'slot-next',
    ];
    const FIELDS       = [
        'slot-prev' => [
            1 => ['sign' => 'Z', 'html' => '0'],
            2 => ['sign' => 'A', 'html' => 'A'],
            3 => ['sign' => 'B', 'html' => 'B'],
            4 => ['sign' => 'C', 'html' => 'C'],
            5 => ['sign' => 'D', 'html' => 'D'],
            6 => ['sign' => 'E', 'html' => 'E'],
            7 => ['sign' => 'Z', 'html' => '0'],
            8 => ['sign' => 'D', 'html' => 'D'],
            9 => ['sign' => 'A', 'html' => 'A'],
            10 => ['sign' => 'E', 'html' => 'E'],
        ],
        'slot-main' => [
            1 => ['sign' => 'A', 'html' => 'A'],
            2 => ['sign' => 'B', 'html' => 'B'],
            3 => ['sign' => 'C', 'html' => 'C'],
            4 => ['sign' => 'D', 'html' => 'D'],
            5 => ['sign' => 'E', 'html' => 'E'],
            6 => ['sign' => 'Z', 'html' => '0'],
            7 => ['sign' => 'D', 'html' => 'D'],
            8 => ['sign' => 'A', 'html' => 'A'],
            9 => ['sign' => 'E', 'html' => 'E'],
            10 => ['sign' => 'Z', 'html' => '0'],
        ],
        'slot-next' => [
            1 => ['sign' => 'B', 'html' => 'B'],
            2 => ['sign' => 'C', 'html' => 'C'],
            3 => ['sign' => 'D', 'html' => 'D'],
            4 => ['sign' => 'E', 'html' => 'E'],
            5 => ['sign' => 'Z', 'html' => '0'],
            6 => ['sign' => 'D', 'html' => 'D'],
            7 => ['sign' => 'A', 'html' => 'A'],
            8 => ['sign' => 'E', 'html' => 'E'],
            9 => ['sign' => 'Z', 'html' => '0'],
            10 => ['sign' => 'A', 'html' => 'A'],
        ]
    ];
    const PAYOUT_TABLE = [
        'CCC' => 10,
        'CCCC' => 50,
        'CCCCC' => 100,
        'AAA' => 5,
        'AAAA' => 20,
        'AAAAA' => 50,
        'BBB' => 10,
        'BBBB' => 50,
        'BBBBB' => 100,
        'DDD' => 2,
        'DDDD' => 5,
        'DDDDD' => 20,
        'EEE' => 2,
        'EEEE' => 10,
        'EEEEE' => 40,
    ];

    //Максимальная ставка
    const BET_MAX = 150;
    
    //Игровое поле
    public $lines;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%' . static::ID . '_ticket}}';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
//            ['betPerLine', 'number', 'min' => FiveBetForm::$limits['betPerLine']['min'], 'max' => FiveBetForm::$limits['betPerLine']['max']],
//            ['linesCount', 'number', 'min' => FiveBetForm::$limits['linesCount']['min'], 'max' => FiveBetForm::$limits['linesCount']['max']],
//            ['denomination', 'number', 'min' => FiveBetForm::$limits['denomination']['min'], 'max' => FiveBetForm::$limits['denomination']['max']],
            ['lottery_id', 'default', 'value' => null],
            ['user_id', 'default', 'value' => getMyId()],
            [['bet', 'user_id', 'paid'], 'required'],
//            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id', 'message'     => Yii::t('frontend', 'Пользователь не найден.')],
            ['bet', JsonValidator::className()],
//            ['paid', 'double', 'min' => 1e-3],
            ['paid', 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
            ['paid', 'compare', 'compareValue' => self::BET_MAX, 'operator' => '<=', 'type' => 'number'],
        ];
    }

    public function calcPaid(array $bet)
    {
        if (isset($bet['betPerLine'], $bet['linesCount'], $bet['denomination'])) {
            $this->paid = $bet['denomination'] * $bet['linesCount'] * $bet['betPerLine'];
            return $this->paid;
        } else {
            throw new BadRequestHttpException('Can\'t calculate paid value');
        }
    }

    /**
     * Розыгрыш слота с учетом уровня отдачи слота
     * @param float $max_return_efficiency Максимальный уровень отдачи автомата. При превышении уровня - выигрышные комбинации игнорируются
     * @param integer $backTimestamp Период подсчёта эффективности
     * @return float
     */
    public function draw($max_return_efficiency = null, $backTimestamp = 6 * Time::SECONDS_IN_A_MONTH)
    {
        if (is_null($max_return_efficiency)) {
            $this->_draw();
            $paid_out = $this->calcPaidOut();
        } else {
            $total_paid             = self::getTotal_Paid($backTimestamp);
            $total_paid_out         = self::getTotal_Paid_Out($backTimestamp);
            $efficiencyOverflowFlag = $total_paid_out / ($total_paid + $this->paid) > $max_return_efficiency / 100;
            do {
                $draw     = $this->_draw();
                $paid_out = $this->calcPaidOut();
            } while ($efficiencyOverflowFlag && (bool) $paid_out);
        }
        return $paid_out;
    }

    /**
     * Произвести розыгрыш
     * @return array
     */
    protected function _draw()
    {
        Yii::$app->random->attributes = self::DRAW_CONFIG;
        $bet                          = $this->_bet;
        $bet['draw']                  = Yii::$app->random->numbers;
        $this->_bet                   = $bet;
        return $bet['draw'];
    }

    /**
     * Расчёт выигрыша
     * @return integer
     */
    public function calcPaidOut()
    {
        $this->paid_out = 0;
        if (!empty($this->_bet['draw'])) {
            $this->expandDraw($this->_bet['draw']);
            $linesDraw = $this->linesDraw($this->lines, $this->_bet['linesCount']);
            $winLines  = [];
            foreach ($linesDraw as $lineNum => $line) {
                $res = $this->normLine($line);
                //Выигрыш, если выпали значащие символы, Z - не значащий
                if (!empty($res) && !in_array($res['sign'], ['Z'])) {
                    $winLines[++$lineNum]             = $res;
                    $winLines[$lineNum]['lineNumber'] = $lineNum;
                    $winLines[$lineNum]['lineWin']    = $this->calcWin($res['value']);
                    $this->paid_out += $winLines[$lineNum]['lineWin']; //Сумма выигрыша
                }
            }
            $this->win_cnt          = count($winLines);
            $this->_win_combination = $winLines;
        }
        return $this->paid_out;
    }

    protected function calcWin($winCode)
    {
        $multiplier = !empty(self::PAYOUT_TABLE[$winCode])?self::PAYOUT_TABLE[$winCode]:0;
        $paid_out = $this->paid * $multiplier;
//        $this->paid_out += $paid_out;
        return $paid_out;
    }

    //Нормализация результата - удаление незначащих символов
    protected function normLine(array $line)
    {
        $cnt = array_count_values($line);
        arsort($cnt, SORT_NUMERIC);
        if (current($cnt) >= 3) {
            return [
                'sign' => key($cnt),
                'count' => current($cnt),
                'value' => str_repeat(key($cnt), current($cnt)),
            ];
        }
    }

    //Распаковка результата в массив в символах игры
    protected function expandDraw(array $draw)
    {
        $lines = [];
        foreach (self::LINES as $line) {
            foreach ($draw as $val) {
                $lines[$line][] = self::FIELDS[$line][$val]['sign'];
            }
        }
        $this->lines = $lines;
        return $lines;
    }

    //Формирует массив результатов по линиям
    protected function linesDraw(array $lines, $linesCount)
    {
        $res = [];
        switch ($linesCount) {
            case 5:
                $res[] = [
                $lines[self::LINES[1]][0],
                $lines[self::LINES[1]][1],
                $lines[self::LINES[2]][2],
                $lines[self::LINES[3]][3],
                $lines[self::LINES[3]][4],
                ];
            case 4:
                $res[] = [
                $lines[self::LINES[3]][0],
                $lines[self::LINES[3]][1],
                $lines[self::LINES[2]][2],
                $lines[self::LINES[1]][3],
                $lines[self::LINES[1]][4],
                ];
            case 3:
                $res[] = $lines[self::LINES[1]];
            case 2:
                $res[] = $lines[self::LINES[3]];
            default:
                $res[] = $lines[self::LINES[2]];
        }
        return $res;
    }

    /**
     * Сумма выигрыша с момента $timestamp по настоящее время
     * @param int $timestamp
     */
    public static function getTotal_Paid_Out($timestamp = 6 * Time::SECONDS_IN_A_MONTH)
    {
        return self::find()
                        ->where(['>', 'created_at', time() - $timestamp])
                        ->sum('paid_out');
    }

    /**
     * Сумма оплат с момента $timestamp по настоящее время
     * @param int $timestamp
     */
    public static function getTotal_Paid($timestamp = 6 * Time::SECONDS_IN_A_MONTH)
    {
        return self::find()
                        ->where(['>', 'created_at', time() - $timestamp])
                        ->sum('paid');
    }

}
