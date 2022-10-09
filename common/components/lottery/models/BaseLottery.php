<?php

namespace common\components\lottery\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\BaseJson;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\commands\AddToTimelineGameCommand;
use yii\helpers\Url;
use common\models\User;
use yii\helpers\Json;

/**
 * BaseLottery
 *
 * @author Mega
 */
abstract class BaseLottery extends ActiveRecord
{

    const ID = null;
    const NAME = '«6 из 45»';
    const DRAW_CONFIG = [
        'count' => 6,
        'max' => 45,
    ];

    public $defaultSuperprize;

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
        return '{{%l' . static::ID . '}}';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'Тираж №',
        ];
    }

    public function rules()
    {
        return [
            ['draw_at', 'default', 'value' => null],
            ['draw_at', 'date', 'timestampAttribute' => 'draw_at', 'format' => 'php:d-m-Y'],
            ['draw_at', 'filter', 'filter' => function($value) {
                    return static::_ceilToDraw($value);
                }],
            ['draw_at', 'unique'],
            ['draw_at', 'compare',
                'compareValue' => time(),
                'operator' => '>',
                'message' => 'Draw time is expired',
                'enableClientValidation' => false,
                'when' => function($model) {
                    return is_null($model->draw);
                }],
        ];
    }

    public function init()
    {
        parent::init();
        $this->on(static::EVENT_AFTER_INSERT, function($event) {
            $addToTimelineCommand = new AddToTimelineGameCommand([
                'category' => 'lottery',
                'event' => 'create',
                'data' => [
                    'id' => $this->id,
                    'type' => static::ID,
                    'draw_at' => $this->draw_at,
                    'created_at' => $this->created_at,
                    'controller' => Yii::$app->controller->id,
                ]
            ]);
            Yii::$app->commandBus->handle($addToTimelineCommand);
        });
    }

    public static function find()
    {
        return new BaseLotteryQuery(get_called_class());
    }

    /**
     * Toggle activity
     */
    public function toggle()
    {
        $this->enabled = !$this->enabled;
        return $this;
    }

    /**
     * Find current Lottery
     * @return
     */
    public static function findCurrent()
    {
        return static::find()
                        ->where([
                            'draw' => null,
                            'enabled' => true,
                        ])
                        ->andWhere(['>', 'draw_at', time()])
                        ->orderBy('draw_at ASC')
                        ->limit(1)
                        ->one();
    }

    /**
     * Ближайшая несыгранная лотерея
     * @param type $timestamp
     * @return type
     */
    public static function findNearest($timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = time();
        }
        return static::find()
                        ->where([
                            'draw' => null,
                            'enabled' => true,
                        ])
                        ->orderBy("ABS(draw_at - $timestamp) ASC")
                        ->limit(1)
                        ->one();
    }

    /**
     * Следующий розыгрыш
     * Если $lottery = null, то результат аналогичен findCurrent()
     * @return
     */
    public static function findNext(BaseLottery $lottery = null)
    {
        $timestamp = is_null($lottery) ? time() : $lottery->draw_at;
        $lotteryNext = static::find()
                ->where([
                    'draw' => null,
                    'enabled' => true,
                ])
                ->andWhere(['>', 'draw_at', $timestamp])
                ->orderBy('draw_at ASC')
//                        ->offset(1)
                ->limit(1)
                ->one();
        if (!$lotteryNext instanceof static) { //FIXME instance of static or BaseLottery
            $lotteryNext = static::createSet($lottery->draw_at + Yii::$app->keyStorage->get('lottery.6x45.interval'), 1);
            $lotteryNext = array_shift($lotteryNext);
        }
        return $lotteryNext;
    }

    /**
     * Самый последний розыгрыш (не сыгранный) в будущем
     * @return
     */
    public static function findLastInFuture()
    {
        return static::find()
                        ->where([
                            'draw' => null,
                            'enabled' => true,
                        ])
                        ->andWhere(['>', 'draw_at', time()])
                        ->orderBy('draw_at DESC')
                        ->limit(1)
                        ->one();
    }

    /**
     * Last finished (с проверкой временного интервала)
     * Завершилась не более чем предыдущий промежуток времени
     * @param integer $timeInterval seconds
     * @return
     */
    public static function findLastFinished($timeInterval = null)
    {
        $query = static::find()
                ->where([
                    'not', ['draw' => null]
                ])
                ->orderBy('draw_at DESC')
                ->limit(1);

        if (is_null($timeInterval)) {
            return $query
                            ->andWhere(['<', 'draw_at', time()])
                            ->one();
        }
        return $query
                        ->andWhere(['<', 'draw_at', time()])
                        ->andWhere(['<', time() . ' - draw_at', $timeInterval])
                        ->one();
    }

    /**
     * Batch Lottery creation
     * @param int $count
     * @param int $period
     * @return [L]
     */
    public static function createSet($firstDrawTimestamp, $count = 1, $period = 86400)
    {
//        $count     = $count < 1 ? 1 : (int) $count;
//        $period            = Yii::$app->keyStorage->get('lottery.5x36.interval');
//        $lastFutureLottery = static::findLastInFuture();
//        $timestamp         = !empty($lastFutureLottery) ? $lastFutureLottery->draw_at : time();
//        $timestamp         = static::_ceilToDraw($timestamp + $period, $period);
        $timestamp = static::_ceilToDraw($firstDrawTimestamp);
        $created = [];
        for ($i = 0; $i < $count; $i++) {
//            $created[] = static::create($timestamp);
            $lottery = new static(['draw_at' => $timestamp]);
            if ($lottery->save()) {
                $created[] = $lottery;
            }
            $timestamp += $period;
        }
        return $created;
    }

    public static function findFutureLotteriesCount()
    {
        return static::find()
                        ->where([
                            'draw' => null,
                            'enabled' => true,
                        ])
                        ->andWhere(['>', 'draw_at', time()])
                        ->count('id');
    }

    /**
     * Create Lottery
     * @param timestamp $draw_at
     * @return L
     * @throws Exception
     */
    //FIXME Перенести в validate модели
//    public static function create($draw_at)
//    {
//        $lottery = new static();
//        if ($draw_at <= time()) {
////            Yii::warning("New lottery draw time [$draw_at] expired. Now is " . time(),'lottery\\' . static::NAME . '\createLottery');
//            throw new Exception("Can't create new lottery. Draw time [$draw_at] expired. Now is " . time());
//        }
//        $concurrentLottery = static::find()
//                ->where(['draw_at' => $draw_at])
//                ->limit(1)
//                ->one();
//        if (!empty($concurrentLottery)) {
////            Yii::warning("New lottery draw time [$draw_at] expired. Now is " . time(),'lottery\\' . static::NAME . '\createLottery');
//            throw new Exception("Can't create new lottery. New is concurrent with lottery №{$concurrentLottery->id} which draw at {$concurrentLottery->draw_dt}.");
//        }
//        $lottery->draw_at = $draw_at;
//        return $lottery;
//    }

    /**
     * Розыгрыш лотереи - определение выигрышной комбинации
     * @return type
     */
    public function draw()
    {
//        $lottery = is_null($lottery) ? static::findCurrent() : $lottery;
//        $randomConfig                 = [
//            'count' => 5,
//            'max'   => 36
//        ];

        Yii::$app->random->attributes = static::DRAW_CONFIG;
        $this->_draw = Yii::$app->random->numbers;
        if ($this->save()) {
            $addToTimelineCommand = new AddToTimelineGameCommand([
                'category' => 'lottery',
                'event' => 'draw',
                'data' => [
                    'id' => $this->id,
                    'type' => static::ID,
                    'draw' => $this->_draw,
                    'draw_at' => $this->draw_at,
                    'created_at' => time(),
                ]
            ]);
            Yii::$app->commandBus->handle($addToTimelineCommand);
            return $this->_draw;
        }
        return null;
    }

    /**
     * Define new lottery date time with feature check
     * @param int $timestamp
     * @return int
     */
    abstract protected static function _ceilToDraw($timestamp);

    public static function ceilToHour($timestamp)
    {
        $dt = getdate($timestamp);
        return mktime($dt['hours'] + 1, 0, 0, $dt['mon'], $dt['mday'], $dt['year']);
    }

    public static function ceilToHalfHour($timestamp)
    {
        $dt = getdate($timestamp);
        $mins = (floor($dt['minutes'] / 30) + 1) * 30;
        return mktime($dt['hours'], $mins, 0, $dt['mon'], $dt['mday'], $dt['year']);
    }

    public static function ceilToQuarterHour($timestamp)
    {
        $dt = getdate($timestamp);
        $mins = (floor($dt['minutes'] / 15) + 1) * 15;
        return mktime($dt['hours'], $mins, 0, $dt['mon'], $dt['mday'], $dt['year']);
    }

    /**
     * Human readable datetime of draw
     */
    public function getDraw_Dt()
    {
        return Yii::$app->formatter->asDatetime($this->draw_at);
    }

    public function getDraw_D()
    {
        return Yii::$app->formatter->asDate($this->draw_at);
    }

    public function set_Draw(array $draw)
    {
        $this->draw = BaseJson::encode($draw);
    }

    public function get_Draw()
    {
        return BaseJson::decode($this->draw);
    }

    public function getDrawReadable()
    {
        $draw = $this->_draw;
        return is_array($draw) ? implode('&nbsp;', $draw) : '';
    }

    public function getSuperprizeReadable()
    {
        return Yii::$app->formatter->asCurrency($this->_superprize, null, [
                \NumberFormatter::MIN_FRACTION_DIGITS => 0,
                \NumberFormatter::MAX_FRACTION_DIGITS => 0,
            ]);
    }

    abstract public function get_Superprize();

    public function set_Wins_Stat(array $stat)
    {
        $this->wins_stat = BaseJson::encode($stat);
    }

    public function get_Wins_Stat()
    {
        return BaseJson::decode($this->wins_stat);
    }

    public function getName()
    {
        return static::NAME;
    }

    public function getType()
    {
        return static::ID;
    }

    //Relations

    /**
     * @return \yii\db\ActiveQuery
     */
//    abstract public function getTickets();
//    abstract public function checkTickets();

    public function getTickets()
    {
        $class = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        return $this->hasMany($class, ['id' => 'lottery_id'])->inverseOf('lottery');
//        return $this->hasMany(L6x45Ticket::className(), ['id' => 'lottery_id'])->inverseOf('lottery');
    }

    public function getClassName()
    {
        $class = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        return $class;
    }

    /**
     * URL Factory
     * @param string $lotteryItem
     */
    public static function UrlFactory(IdInterface $lotteryItem, $action = 'index')
    {
        return Url::to('/lottery' . $lotteryItem->getLotteryId() . "/$action");
    }

    /**
     * Make bets for current lottery (test method)
     * @param type $count
     * @return type
     */
    public static function _createBets($count = 10)
    {
        $users = [User::findIdentity(3), User::findIdentity(9)];
        $lottery = static::findCurrent();
        Yii::$app->random->attributes = [
        'count' => 6,
        'max' => 45,
    ];;

        $class = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        $created = [];
        for ($i = 0; $i < $count; $i++) {
            $k = array_rand($users);
            $ticket = new $class(['lottery_id' => $lottery->id, 'bet' => Json::encode(Yii::$app->random->numbers), 'user_id' => $users[$k]->id]);
            if ($ticket->save()) {
                $created[] = $ticket;
            }
        }
        return $created;
    }

}
