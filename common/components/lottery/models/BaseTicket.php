<?php

namespace common\components\lottery\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\UserProfile;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use common\commands\AddToTimelineGameCommand;

/**
 * Base Lottery Ticket Class
 *
 * @author Mega
 */
abstract class BaseTicket extends ActiveRecord implements IdInterface
{

    const ID          = null;
    const MIN_NUMBERS = 6;
    const MAX_NUMBERS = 13;
    const NAME        = '«6 из 45»';

    public $pricing = [];

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
    public function rules()
    {
        return[
            ['user_id', 'default', 'value' => getMyId()],
            ['bet', 'validateTicket'],
            [['bet', 'user_id', 'lottery_id', 'paid'], 'required'],
            ['paid', 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%l' . static::ID . '_ticket}}';
    }

    public function init()
    {
        parent::init();
        $this->pricing = Json::decode(Yii::$app->keyStorage->get('lottery.' . static::ID . '.pricing'));

        //Снять деньги за ставку
        $this->on(static::EVENT_BEFORE_INSERT, function($event) {
            $this->paid();
        });
        //Внести в timeline
        $this->on(static::EVENT_AFTER_INSERT, function($event) {
            $addToTimelineCommand = new AddToTimelineGameCommand([
                'category' => 'bet',
                'event' => 'create',
                'data' => [
                    'id' => $this->id,
                    'user_id' => $this->user_id,
                    'lottery_id' => $this->lottery_id,
                    'lottery_type_id' => static::ID,
                    'type' => static::NAME,
                    'numbers' => implode('; ', isset($this->_bet['draw']) ? $this->_bet['draw'] : $this->_bet),
                    'paid' => $this->paid,
                    'created_at' => $this->created_at,
                ]
            ]);
            Yii::$app->commandBus->handle($addToTimelineCommand);
        });
    }

//    public function beforeSave($insert)
//    {
//        if (parent::beforeSave($insert)) {
//            if (empty($this->paid)) {
//                $this->calcPaid($this->_bet);
//            }
//            $this->userProfile->account -= $this->paid;
//            if ($this->userProfile->account > 0) {
//                if ($this->userProfile->save()) {
//                    return true;
//                }
//            }
//            else {
//                throw new \yii\base\UserException('Insufficient funds');
//            }
//        }
//        return false;
//    }
    //Оплата билета
    protected function paid()
    {
        if (empty($this->paid)) {
            $this->calcPaid($this->_bet);
        }
        $this->userProfile->AccountWithdraw($this->paid, static::ID . ' lottery bet');
        $this->userProfile->save();
    }

    //Выплата выигрыша
    public function paidOut()
    {
        $this->userProfile->AccountCharge($this->paid_out, static::ID . ' lottery win');
        $this->userProfile->save();
    }

    /**
     *
     * @param array $bet
     */
    public function set_Bet(array $bet)
    {
        $this->bet = Json::encode($bet);
        $this->calcPaid($bet);
    }

    /**
     *
     * @return array
     */
    public function get_Bet()
    {
        return Json::decode($this->bet);
    }

    public function getPaidReadable()
    {
        return Yii::$app->formatter->asCurrency($this->paid);
    }

    public function getPaid_out_Readable()
    {
        return Yii::$app->formatter->asCurrency((double) $this->paid_out);
    }

    /**
     *
     * @param array $numbers
     */
    public function set_Win_Combination(array $numbers)
    {
        $this->win_combination = Json::encode($numbers);
    }

    /**
     *
     * @return array
     */
    public function get_Win_Combination()
    {
        return Json::decode($this->win_combination);
    }

    public function getWin_combinationReadable()
    {
        return implode(', ', $this->_Win_Combination);
    }

    public function getName()
    {
        return static::NAME;
    }

    public function getType()
    {
        return static::ID;
    }

    public function getLotteryId()
    {
        return static::ID;
    }

    public function beforeValidate()
    {
        $res = parent::beforeValidate();
        if (empty($this->paid)) {
//            try {
            $res = $res && $this->calcPaid($this->_bet);
//            } catch (TypeError $e) {
//                $res = false;
//            }
        }
        return $res;
    }

    /**
     * Validator for ticket numbers
     * @param array $attribute
     * @param array $params
     */
    public function validateTicket($attribute, $params)
    {
        if (!is_array($this->$attribute)) {
            $attr = Json::decode($this->$attribute);
        }
        $count = count($attr);
        if ($count < static::MIN_NUMBERS or $count > static::MAX_NUMBERS) {
            $this->addError([$attribute, $attr], static::NAME . ". Ticket error. Wrong numbers count [$count].");
        }

        //Проверка повторяющихся номеров
        $uniqCount = count(array_unique($attr));
        if ($count != $uniqCount) {
            $this->addError([$attribute, $attr], static::NAME . '. Ticket error. ' . implode(', ', $attr) . ' Doubled Numbers!');
        }
    }

    /**
     * Призовой фонд
     */
    public static function getPool(BaseLottery $lottery)
    {
        return static::find()->where(['lottery_id' => $lottery->id])->sum('[[paid]]');
    }

    public static function create(BaseLottery $lottery, User $user, $paid, array $bet)
    {
        $userProfile = $user->userProfile;
        try {
            $userProfile->AccountWithdraw($paid, static::ID . ' lottery bet');
        } catch (Exception $ex) {
            throw $ex;
        }

        if (!$userProfile->save()) {
            Yii::warning([
                'message' => 'Money withdraw error',
                'user' => $user,
                'paid' => $paid,
                    ], 'lottery\\' . self::NAME . '\createBet'
            );
            throw new Exception('Money withdraw error'); //Ошибка списания средств
//            return null;
        }

        $lBet = new static([
            'lottery_id' => $lottery->id,
            'user_id' => $user->id,
            'paid' => $paid,
            '_bet' => $bet
        ]);

        if ($lBet->save()) {
            $addToTimelineCommand = new AddToTimelineGameCommand([
                'category' => 'bet',
                'event' => 'create',
                'data' => [
                    'id' => $lBet->id,
                    'user_id' => $lBet->user_id,
                    'lottery_id' => $lBet->lottery_id,
                    'lottery_type_id' => static::ID,
                    'type' => static::NAME,
                    'numbers' => implode('; ', $bet),
                    'paid' => $lBet->paid,
                    'created_at' => $lBet->created_at,
                ]
            ]);
            Yii::$app->commandBus->handle($addToTimelineCommand);
        }

        return $lBet;
    }

    /**
     * Batch tickets creation for lottery (test method)
     * @param int $count
     * @return [BaseTicket]
     */
    public static function _createSet(BaseLottery $lottery, $count = 1, array $randomConfig = [ 'count' => 6, 'max' => 45], array $users)
    {
//        $users                        = [User::findIdentity(5), User::findIdentity(4)];
//        $lottery                      = L5x36::findCurrent();
//        $randomConfig                 = [
//            'count' => 5,
//            'max'   => 36,
//        ];
        Yii::$app->random->attributes = $randomConfig;

        $created = [];
        for ($i = 0; $i < $count; $i++) {
            $k = array_rand($users);
            try {
                $created[] = static::create($lottery->id, $users[$k], 10, Yii::$app->random->numbers);
            } catch (\yii\db\Exception $e) {
                
            }
        }
        return $created;
    }

    /**
     * Расчёт числа выигрышей по уровням.
     * Уровень 1 - число выигрышных комбинаций при максимальном совпадении.
     * @param type $bet_cnt Число цифр в ставке
     * @param type $win_cnt Число цифр выигравших
     * @param type $default_cnt Число цифр в розыгрыше
     * @param type $level Уровень расчёта статистики
     * @return int Число комбинаций
     */
    public static function betWinsStat($bet_cnt, $win_cnt, $level = 1)
    {
        $comb = function($n, $k) {
            return ($n >= $k) ? (int) (gmp_fact($n) / gmp_fact($k) / gmp_fact($n - $k)) : 0;
        };

        switch ($level) {
            case 1:
                $n  = $bet_cnt - $win_cnt;
                $k  = static::MIN_NUMBERS - $win_cnt;
                return $comb($n, $k);
                break;
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
                $n1 = $win_cnt;
                $k1 = $win_cnt - $level + 1;
                $n2 = $bet_cnt - $win_cnt;
                $k2 = $level;
                return (int) ($comb($n1, $k1) * $comb($n2, $k2));
                break;
            default :
                return 0;
        }
    }

    /**
     * Расчёт выигрыша для одного билета
     * @param type $bet_cnt
     * @param type $win_cnt
     * @param array $winStat
     */
    public static function betWin($bet_cnt, $win_cnt, array $statExtended)
    {
        $sum = 0;
        for ($correctNumbers = 2; $correctNumbers <= $win_cnt; $correctNumbers++) {
            $level = $win_cnt - $correctNumbers + 1;
            $res   = static::betWinsStat($bet_cnt, $win_cnt, $level);
            $sum +=static::betWinsStat($bet_cnt, $win_cnt, $level) * $statExtended[$correctNumbers]['paidoutOne'];
        }
        return $sum;
    }

    //Relations
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'user_id']);
    }

    public function calcPaid(array $bet)
    {
        if (isset($this->pricing[count($bet)])) {
            $this->paid = $this->pricing[count($bet)];
            return $this->paid;
        } else {
            throw new BadRequestHttpException('Ошибка расчёта цены билета: ' . print_r($bet, 1), 422);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    abstract public function getLottery();

    /**
     * @return \yii\db\ActiveQuery
     */
//    abstract public function getDraw();

    public function getDraw()
    {
//        return $this->hasMany(L6x45Draw::className(), [ 'lottery_id' => 'id'])->inverseOf('ticket');
        $class = __NAMESPACE__ . '\\L' . static::ID . 'Draw';
        return $this->hasMany($class, ['id' => 'lottery_id'])->inverseOf('ticket');
    }

    public function getLottery()
    {
        $class = __NAMESPACE__ . '\\L' . static::ID;
        return $this->hasOne($class, [ 'id' => 'lottery_id']);
//        return $this->hasOne(L6x45::className(), [ 'lottery_id' => 'id']);
    }

}
