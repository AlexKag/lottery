<?php

namespace common\base;

//use yii\web\UserEvent;
use yii\base\Event;

/**
 * PaymentEvent - списание средств со счёта пользователя
 *
 * @author Mega
 */
class PaymentEvent extends Event
{
    public $userProfile;
    public $isValid = true; //Флаг обработки события
    public $amount; //Сумма
    public $reason; //Комментарий
    public $system; //Платежная система
    public $operation_id;
    public $direction; //Направление: in - ввод, out - вывод
    public $status; //Направление: in - ввод, out - вывод
    public $target; //Номер счёта, на который производится вывод средств

//    public $withHandlingFee;
//    public $withdraw;
}
